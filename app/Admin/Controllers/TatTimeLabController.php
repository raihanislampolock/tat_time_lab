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

class TatTimeLabController extends AdminController
{
    private $service_title;
    private $service_name;

    public function __construct()
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "https://ept.praavahealth.com/API/PatientPortal/ServiceMasterApp?token=03e62234b7238ca3eab782f30b9dfa94&code=service",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );
        try {
            $response = curl_exec($curl);
            // dd($response);
            if (!is_null($response)) {
                $responseData = json_decode($response, true);
                if (isset($responseData['result'])) {
                    $title = [];
                    foreach ($responseData['result'] as $item) {
                        $title[$item['id']] = $item['service_name'];
                    }
                    $this->service_title = $title;
                }

                return $this->service_title;
            }
        } catch (\Exception $exception) {
            \Log::info(json_encode($exception));
        }
    }
    protected function script()
    {
        return <<<EOT
            $(document).ready(function() {
                $(".service_list").on("change", function() {
                    serviceNames();
                    myFunction(this);
                });
            });
    
            function serviceNames() {
                if ($(".service_list").find(":selected").val()) {
                    let serviceName = $(".service_list option:selected").text();
                    $(".service_name").val(serviceName);
                }
            }
            
            window.myFunction = function(selectElement) {
                $(selectElement).on("change", function() {
                    var selectedServiceId = $(this).val();
                    var matchingData = ""; // Initialize an empty string to accumulate matching data.
            
                    $.ajax({
                        url: "/admin/get-lab-tat",
                        type: "GET",
                        data: { selectedServiceId: selectedServiceId },
                        success: function(response) {
                            if (response) {
                                var found = false;
                                for (var i = 0; i < response.length; i++) {
                                    if (response[i].service_id == selectedServiceId) {
                                        matchingData += "<b>Service Name:</b> " + response[i].service_name + " <b>Start Time:</b> " + response[i].start_time + " <b>End Time:</b> " + response[i].end_time + " <b>Days:</b> " + response[i].days + " <b>Report Delevary:</b> " + response[i].report_delevary + "<br><br>";
                                        found = true;
                                    }
                                }
            
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
                });
            };
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
        $grid->TestType()->name('Test Type');
        $grid->column('start_time', __('Start time'));
        $grid->column('end_time', __('End time'));
        $grid->column('days', __('Days'));
        $grid->column('report_delevary', __('Report delevary'));
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
        $show->field('report_delevary', __('Report delevary'));
        $show->field('status', __('Status'));
        $show->field('cb', __('Cb'));
        $show->field('cd', __('Cd'));
        $show->field('ub', __('Ub'));
        $show->field('ud', __('Ud'));

        return $show;
    }

    public function showTat(){
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

        $form->select('service_id', __('Choose A Service'))->addElementClass('service_list')->options($this->service_title)->rules('required')->attribute('onchange', 'myFunction(this)');
        $form->hidden('service_name', __('Service name'))->addElementClass('service_name');
        $form->html('<div id="show"></div>');

        $Test = TestType::pluck('name', 'id')->toArray();
        $form->select('b2b_b2c', __('Test Type'))->options($Test);
        $form->datetime('start_time', __('Start time'));
        $form->datetime('end_time', __('End time'));
        $form->datetime('report_delevary', __('Report delevary'));
        $form->number('days', __('Days'));
        $form->switch('status', __('Status'))->default(1);
        $form->hidden('cb', __('Cb'))->value(auth()->user()->name);
        $form->hidden('ub', __('Ub'))->value(auth()->user()->name);

        return $form;
    }
    

}