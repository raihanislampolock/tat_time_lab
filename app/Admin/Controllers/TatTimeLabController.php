<?php

namespace App\Admin\Controllers;

use App\Models\TatTimeLab;
use App\Models\TestType;
use App\Services\MasterServiceList;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TatTimeLabController extends AdminController
{
    private $service_title;
    private $service_name;
    private $category;

    public function __construct()
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'http://api.praava.health/api/service_list',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'auth: 811d5252b43ede3da0686aa828ff2e12',
                ),
            )
        );

        try {
            $response = curl_exec($curl);
            $responseData = json_decode($response);

            if (isset($responseData)) {
                $title = [];
                foreach ($responseData as $item) {
                    $title[$item->service_id] = $item->service_name . ' - ' . $item->category;
                }
                $this->service_title = $title;
            }
        } catch (Exception $exception) {
            Log::info(json_encode($exception));
        }
    }


    protected function script()
    {
        return <<<EOT
        $(document).ready(function() {
            $(".service_list").on("change", function() {
                serviceNames();
                serviceCategory();
                let selectedServiceId = $(this).val();
                if (selectedServiceId) {
                    $.ajax({
                        url: "/admin/get-lab-tat",
                        type: "GET",
                        data: { selectedServiceId: selectedServiceId },
                        success: function(response) {
                            if (response) {
                                let found = false;
                                let matchingData = "";
                                response.forEach(function(item) {
                                    if (item.service_id == selectedServiceId) {
                                        matchingData += "<b>Service Name:</b> " + item.service_name + " <b>Start Time:</b> " + item.start_time + " <b>End Time:</b> " + item.end_time + " <b>Days:</b> " + item.days + " <b>Report Delivery:</b> " + item.report_delivery + "<br><br>";
                                        found = true;
                                    }
                                });                                
        
                                if (found) {
                                    document.getElementById("show").innerHTML = matchingData;
                                } else {
                                    document.getElementById("show").innerHTML = "No data found for the selected service ID.";
                                }
                            } else {
                                console.log("Response is empty or undefined.");
                            }
                        },
                        error: function(error) {
                            console.log("An error occurred:", error);
                        }
                    });
                }
            });
        });
        
        function serviceNames() {
            if ($(".service_list").find(":selected").val()) {
                let serviceName = $(".service_list option:selected").text();
                let parts = serviceName.split(' - ');
                if (parts.length === 2) {
                    $(".service_name").val(parts[0]);
                    $(".category").val(parts[1]);
                }
            }
        }
        
        function serviceCategory() {
            if ($(".service_list").find(":selected").val()) {
                let serviceName = $(".service_list option:selected").text();
                let parts = serviceName.split(' - ');
                if (parts.length === 2) {
                    $(".service_name").val(parts[0]);
                    $(".category").val(parts[1]);
                }
            }
        }
         
        EOT;
    }


    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'TatTimeLab';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TatTimeLab());

        $grid->column('id', __('Id'));
        $grid->column('service_id', __('Service id'));
        $grid->column('service_name', __('Service name'));
        $grid->column('test_type', __('Test Type'));
        // $grid->TestType()->name('Test Type');
        $grid->column('start_time', __('Start time'));
        $grid->column('end_time', __('End time'));
        $grid->column('days', __('Days'));
        $grid->column('report_delivery', __('Report delivery'));
        $grid->column('status', __('Status'))->display(function ($status) {
            return $status ? '<span style=" color: green; font-weight:900;">Active</span>' :
                '<span style="color: red; font-weight:900;">Inactive</span>';
        });
        $grid->column('cd', __('Cd'));

        $grid->model()->orderBy('id', 'desc');



        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(TatTimeLab::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('service_id', __('Service id'));
        $show->field('service_name', __('Service name'));
        $show->field('test_type', __('Test Type'));
        $show->field('start_time', __('Start time'));
        $show->field('end_time', __('End time'));
        $show->field('days', __('Days'));
        $show->field('report_delivery', __('Report delivery'));
        $show->field('status', __('Status'));
        $show->field('cb', __('Cb'));
        $show->field('cd', __('Cd'));
        $show->field('ub', __('Ub'));
        $show->field('ud', __('Ud'));

        return $show;
    }

    public function showTat()
    {
        $data = TatTimeLab::all();
        return response()->json($data);
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Admin::script($this->script());
        $form = new Form(new TatTimeLab());

        $form->select('service_id', __('Choose A Service'))->addElementClass('service_list')->options($this->service_title)->rules('required');
        $form->hidden('service_name', __('Service name'))->addElementClass('service_name');
        $form->hidden('test_type', __('Test Type'))->addElementClass('category');
        $form->html('<div id="show"></div>');

        // $Test = TestType::pluck('name', 'id')->toArray();
        // $form->select('b2b_b2c', __('Test Type'))->options($Test);
        $form->time('start_time', __('Start time'))->format('hh:mm A');
        $form->time('end_time', __('End time'))->format('hh:mm A');
        $form->time('report_delivery', __('Report delivery'))->format('hh:mm A');
        $form->number('days', __('Days'));
        $form->switch('status', __('Status'))->default(1);
        $form->hidden('cb', __('Cb'))->value(auth()->user()->name);
        $form->hidden('ub', __('Ub'))->value(auth()->user()->name);

        return $form;
    }
}
