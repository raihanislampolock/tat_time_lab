<?php

use App\Models\Gco\GcoOrder;
use App\Models\Gco\GcoOrderHistory;
use App\Models\ShopConfig;
use Encore\Admin\Facades\Admin;

//use Gloudemans\Shoppingcart\Facades\Cart;
use Gnugat\NomoSpaco\File\FileRepository;
use Gnugat\NomoSpaco\FqcnRepository;
use Gnugat\NomoSpaco\Token\ParserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

//use App\Models\ShopCategory;
//use App\Models\ShopProduct;
//use Intervention\Image\Facades\Image;

//if (!function_exists('get_shop_products')) {
//
////    function get_shop_products()
////    {
////
////        $shopProducts = Cache::get('shop_products', config('shop_cache_time'), function () {
////            return ShopProduct::where('status', 1)->orderBy('sort', 'ASC')->get();
////        });
////        return $shopProducts;
////    }
//
//}

//
//
///**
// * @param $data
// * @return int
// */
//function create_customer($data)
//{
//    try {
//        $customer = ShopCustomer::where('phone', 'like', '%' . $data['phone'] . '%')->first();
//        if ($customer) {
//        } else {
//            $customer = new ShopCustomer();
//        }
//        $customer->phone = $data['phone'];
//        $customer->name = $data['name'];
//        $customer->email = $data['email'];
//        $customer->gender = $data['gender'] ?? 'other';
//        $customer->other_email = $data['other_email'] ?? '';
//        $customer->date_of_birth = $data['date_of_birth'] ?? date('Y-m-d');
//        $customer->occupation = $data['occupation'] ?? '';
//        $customer->organization = $data['organization'] ?? '1';
//        // $customer->address1 = $data['address1'];
//        $customer->password = bcrypt($data['phone']);
//        $customer->save();
//        return $customer->id;
//    } catch (Exception $ex) {
//        Log::error(__FILE__ . ' Line No ' . __LINE__ . '| ' . 'customer not save ' . $ex->getMessage() . ' data ' . json_encode($data));
//        return 0;
//    }
//}
//
///**
// * @param $data
// * @return int
// */
//function create_customer_address($data)
//{
//    try {
//        $customer = null;
//        if (array_key_exists('id', $data)) {
//            $customer = ShopCustomerAddress::where('id', '=', $data['id'])->first();
//        }
//        if ($customer) {
//        } else {
//            $customer = ShopCustomerAddress::where('customer_id', '=', $data['customer_id'])->where('address_name', '=', $data['address_name'])->where('name', '=', $data['name'])->where('phone', '=', $data['phone'])->where('email', '=', $data['email'])->where('city', '=', $data['city'])->where('area', '=', $data['area'])->where('thana', '=', $data['thana'])->where('post_code', '=', $data['post_code'])->where('address', '=', $data['address'])->first();
//            if ($customer) {
//            } else {
//                $customer = new ShopCustomerAddress();
//            }
//        }
//        // $customer = new ShopCustomerAddress();
//        $customer->customer_id = $data['customer_id'];
//        $customer->address_name = $data['address_name'];
//        $customer->name = $data['name'];
//        $customer->phone = $data['phone'] ?? 'phone';
//        $customer->email = $data['email'] ?? '';
//        $customer->country = $data['country'];
//        $customer->division = $data['division'] ?? '';
//        $customer->city = $data['city'] ?? '';
//        $customer->area = $data['area'];
//        $customer->thana = $data['thana'];
//        $customer->post_code = $data['post_code'];
//        $customer->address = $data['address'];
//        $customer->status = $data['status'] ?? 'active';
//        $customer->save();
//        return $customer->id;
//    } catch (Exception $ex) {
//        Log::error(__FILE__ . ' Line No ' . __LINE__ . '| ' . 'customer address  not save ' . $ex->getMessage() . ' data ' . json_encode($data));
//        return 0;
//    }
//}

/**
 * @param $num
 * @return string
 */

function numberTowords($num)
{
    $ones = array(
        1 => "one",
        2 => "two",
        3 => "three",
        4 => "four",
        5 => "five",
        6 => "six",
        7 => "seven",
        8 => "eight",
        9 => "nine",
        10 => "ten",
        11 => "eleven",
        12 => "twelve",
        13 => "thirteen",
        14 => "fourteen",
        15 => "fifteen",
        16 => "sixteen",
        17 => "seventeen",
        18 => "eighteen",
        19 => "nineteen"
    );
    $tens = array(
        1 => "ten",
        2 => "twenty",
        3 => "thirty",
        4 => "forty",
        5 => "fifty",
        6 => "sixty",
        7 => "seventy",
        8 => "eighty",
        9 => "ninety"
    );
    $hundreds = array(
        "hundred",
        "thousand",
        "million",
        "billion",
        "trillion",
        "quadrillion"
    ); //limit t quadrillion
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {
        if ($i < 20) {
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            $rettxt .= $tens[substr($i, 0, 1)];
            $rettxt .= " " . $ones[substr($i, 1, 1)];
        } else {
            $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
            $rettxt .= " " . $tens[substr($i, 1, 1)];
            $rettxt .= " " . $ones[substr($i, 2, 1)];
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }
    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            $rettxt .= $ones[$decnum];
        } elseif ($decnum < 100) {
            $rettxt .= $tens[substr($decnum, 0, 1)];
            $rettxt .= " " . $ones[substr($decnum, 1, 1)];
        }
    }
    return $rettxt;
}


function client_unique_id()
{
    $computerId = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];
    return md5($computerId);
}


/**
 * @return string
 */
function current_url()
{
    $current_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $current_url .= $_SERVER["SERVER_NAME"];
    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $current_url .= ":" . $_SERVER["SERVER_PORT"];
    }
    $current_url .= $_SERVER["REQUEST_URI"];
    return $current_url;
}

/**
 * @param $string
 * @return string|string[]|null
 */
function stringToNumber($string)
{
    return preg_replace("/[^0-9.]/", "", $string);
}

/**
 * @param $from
 * @param $to
 * @param string $format
 * @return string
 * @throws Exception
 */
function datetime_diffrence($from, $to, $format = '%a')
{
    $origin = new DateTime(date('Y-m-d H:i:s', strtotime($from)));
    $target = new DateTime(date('Y-m-d H:i:s', strtotime($to)));
    $interval = $origin->diff($target);
    return $interval->format($format);
}

/**
 * @param $group
 * @param bool $onlyActive
 * @return mixed
 */

//Extensions
function getExtensionsGroup($group, $onlyActive = true)
{
    $group = ucfirst($group);
    return ShopConfig::getExtensionsGroup($group, $onlyActive);
}

/**
 * @return FqcnRepository
 */
function start()
{
    $fileRepository = new FileRepository();
    $parserFactory = new ParserFactory();

    return new FqcnRepository($fileRepository, $parserFactory);
}

/**
 * @param $folder
 * @param null $group
 * @return array
 */
function classNames($folder, $group = null)
{
    $group = ucfirst($group);
    $arrModules = [];
    $path = app_path() . '/' . $folder . '/' . $group . '/Controllers';
    $modules = start()->findIn($path);
    if ($modules) {
        foreach ($modules as $key => $module) {
            $arrTmp = explode('\\', $module);
            $arrModules[] = end($arrTmp);
        }
    }

    return $arrModules;
}

/**
 * @param $folder
 * @param null $group
 * @param null $module
 * @return array
 */
function findClassNames($folder, $group = null, $module = null)
{
    $group = ucfirst($group);
    $path = app_path() . '/' . $folder . '/' . $group . '/Controllers';
    if ($module) {
        return start()->findInFor($path, $module);
    } else {
        return start()->findIn($path);
    }
}

/**
 * @param $plaintext
 * @return false|string
 */

function opensslEncrypt($plaintext)
{
    $ciphering = "AES-128-CTR";
    $options = 0;
    $encryption_iv = '1234567891011121';
    $encryption_key = "dotlineCMS";

    return openssl_encrypt($plaintext, $ciphering, $encryption_key, $options, $encryption_iv);

}

/**
 * @param $ciphertext
 * @return false|string
 */
function opensslDecrypt($ciphertext)
{
    $ciphering = "AES-128-CTR";
    $decryption_iv = '1234567891011121';
    $decryption_key = "dotlineCMS";
    $options = 0;
    return openssl_decrypt($ciphertext, $ciphering, $decryption_key, $options, $decryption_iv);
}

///**
// * @param $mcnt_TxnNo
// * @return bool|string
// */
//function checkTransactionStatus($mcnt_TxnNo, $url)
//{
//    try {
//        //    $url = config('shop.payment_status_api');
//        $SecurityKey = config('shop.secretkey');
//        $mcnt_SecureHashValue = md5($SecurityKey . $mcnt_TxnNo);
//        $url = $url . '?mcnt_TxnNo=' . $mcnt_TxnNo . '&mcnt_SecureHashValue=' . $mcnt_SecureHashValue;
//
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'GET',
//        ));
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//        curl_close($curl);
//        Log::channel('payment')->info(__FILE__ . ' Line No ' . __LINE__ . 'response' . $response . ' |err ' . $err . ' |url ' . $url);
//        return $response;
//    } catch (\Exception $ex) {
//        Log::channel('payment')->error(__FILE__ . ' Line No ' . __LINE__ . '| item ID ' . $ex->getMessage() . '| ' . $ex->getTraceAsString());
//        return false;
//    }
//}

/**
 * @param $order_id
 * @param string $content
 * @param int $admin_id
 */
/*
function orderHistoryAdd($order_id, $content = '', $other = array('admin_id' => 0, 'order_detail_id' => 0, 'order_status' => 0, 'shipping_status' => 0))
{
    $admin_id = $other['admin_id'] ?? 0;
    if ($admin_id == 0) {
        $admin_id = Admin::user()->id ?? 0;
    }
    $dataHistory = [
        'order_id' => $order_id,
        'content' => $content,
        'admin_id' => $admin_id,
        'order_detail_id' => $other['order_detail_id'] ?? 0,
        'order_status' => $other['order_status'] ?? 0,
        'shipping_status' => $other['shipping_status'] ?? 0,

        'created_at' => date('Y-m-d H:i:s'),
    ];
    ShopOrderHistory::insert($dataHistory);
}*/


/**
 * @param string $request_method
 * @param array $param
 * @param string $url
 * @param array $header
 * @param string $requestBodyType
 * @param bool $decodeResponse
 * @return array|mixed|\Psr\Http\Message\ResponseInterface
 */
function makeHttpRequest(string $request_method, array $param, string $url, array $header = [], string $requestBodyType = 'json', bool $decodeResponse = true)
{
    if (empty($header))
        $header = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json'
        ];

    switch ($request_method) {
        case 'post':
            try {
                $client = new GuzzleHttp\Client(['headers' => $header, 'verify' => false]);
                $response = $client->post($url, [$requestBodyType => $param]);
                if ($decodeResponse)
                    $response = GuzzleHttp\json_decode((string)$response->getBody(), true);
                return $response;
            } catch (GuzzleHttp\Exception\RequestException $e) {
                Log::error("Request Exception Happened when tried to call: " . $url, [
                    'message' => empty($e->getResponse()) ? $e->getMessage() : ($e->getResponse()->getBody() ?? $e->getMessage()),
                    'params' => $param,
                    'trace' => $e->getTraceAsString()
                ]);
                return [
                    'message' => empty($e->getResponse()) ? $e->getMessage() : ($e->getResponse()->getBody() ?? $e->getMessage()),
                    'params' => $param,
                    'trace' => $e->getTraceAsString()
                ];
            } catch (Exception $e1) {
                Log::error("Exception Happened when tried to call: " . $url, [
                    'message' => $e1->getMessage(),
                    'params' => $param,
                    'trace' => $e1->getTraceAsString()
                ]);
                return [
                    'message' => $e1->getMessage(),
                    'params' => $param,
                    'trace' => $e1->getTraceAsString()
                ];
            }
            break;
        default:
            try {
                $client = new GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,), 'headers' => $header,));
                $response = $client->get($url . '?' . http_build_query($param));
                if ($decodeResponse)
                    $response = GuzzleHttp\json_decode((string)$response->getBody(), true);
                return $response;
            } catch (GuzzleHttp\Exception\RequestException $e) {
                Log::error("Request Exception Happened when tried to call: " . $url, [
                    'message' => empty($e->getResponse()) ? $e->getMessage() : ($e->getResponse()->getBody() ?? $e->getMessage()),
                    'params' => $param,
                    'trace' => $e->getTraceAsString()
                ]);
                return [
                    'message' => empty($e->getResponse()) ? $e->getMessage() : ($e->getResponse()->getBody() ?? $e->getMessage()),
                    'params' => $param,
                    'trace' => $e->getTraceAsString()
                ];
            } catch (Exception $e1) {
                Log::error("Exception Happened when tried to call: " . $url, [
                    'message' => $e1->getMessage(),
                    'params' => $param,
                    'trace' => $e1->getTraceAsString()
                ]);
                return [
                    'message' => $e1->getMessage(),
                    'params' => $param,
                    'trace' => $e1->getTraceAsString()
                ];
            }
            break;
    }
}

//
///**
// * @param string $gateway
// * @param string $credentialKey
// * @return array
// */
//function get_gateway_credentials(string $gateway, string $credentialKey): array
//{
//    return config("smsgw.$gateway.credentials.$credentialKey");
//}
//
///**
// * @param $gateway
// * @param $credentialKey
// * @param $mobile
// * @param $message
// * @return array
// */
//function get_gateway_request_params($gateway, $credentialKey, $mobile, $message)
//{
//    $request_params = get_gateway_credentials($gateway, $credentialKey);
//
//    switch ($gateway) {
//        case 'etracker':
//            $request_params['to'] = $mobile;
//            $request_params['text'] = $message;
//            break;
//        default:
//            $request_params['receiver'] = $mobile;
//            $request_params['message'] = $message;
//            break;
//    }
//
//    return $request_params;
//}
//
//
///**
// * @param string $mobile
// * @param string $message
// * @param string $gateway
// * @param string $credentialKey
// * @return bool
// */
//function sendSms(string $mobile, string $message, $order_id = 0, string $gateway = 'boomcast', string $credentialKey = "default")
//{
//    $message_id = '';
//    $status_message = '';
//    $sms_send_status = '';
//    $credentials = collect(config("smsgw." . $gateway));
//    $response = makeHttpRequest($credentials->get('http_request_method'), get_gateway_request_params($gateway, $credentialKey, $mobile, $message), $credentials->get('api_url'));
//    if (empty($response)) {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '| no response found ' . json_encode($response));
//        return false;
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '| sms response ' . json_encode($response));
//    }
//
//    switch ($gateway) {
//        case 'etracker':
//            $data = explode(',', $response);
//            $sms_send_status = 0;
//            $message_id = '';
//            $status_message = $response;
//            if ($data[0]) {
//                if ($data[0] == $mobile) {
//                    $message_id = $data[1] ?? '';
//                    $sms_send_status = 1;
//                    $status_message = $data[2] ?? '';
//                } else {
//                    $sms_send_status = 0;
//                    $status_message = $data[0] ?? '';
//                }
//            }
//            break;
//        default:
//            $message_id = $response[0]['msgid'] ?? '';
//            $status_message = $response[0]['message'] ?? '';
//            $sms_send_status = $response[0]['success'] ?? '';
//            break;
//    }
//    try {
//        DB::table('shop_sms_logs')->insert(['order_id' => $order_id, 'status' => $sms_send_status, 'mobile_no' => $mobile, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'message_id' => $message_id, 'status_message' => $status_message]);
//    } catch (Exception $e) {
//
//    }
//
//    return $sms_send_status;
//}
//
//function orderPaymentUpdate($transaction_id, $calling_from = '')
//{
//    $shopTransaction = ShopTransaction::find($transaction_id);
//    $amount_receive = 0;
//    if ($shopTransaction) {
//        $transaction_remaining = $shopTransaction->received;
//        foreach ($shopTransaction->shop_transaction_order as $order) {
//            $shopOrder = ShopOrder::find($order->id);
//            if ($shopOrder) {
//                if ($shopTransaction->payment_status == 2) {
//                    $order_total_data = $shopOrder->order_total;
//                    if ($order_total_data) {
//                        foreach ($order_total_data as $val) {
//                            if ($val->code == 'received') {
//                                $orderTotal = ShopOrderTotal::find($val->id);
//                                if ($shopOrder->total <= $transaction_remaining) {
//                                    $orderTotal->value = $orderTotal->value + $shopOrder->total;
//                                    $transaction_remaining = $transaction_remaining - $shopOrder->total;
//                                    $shopOrder->received = $shopOrder->received + $shopOrder->total;
//                                    $shopOrder->payment_status = $shopTransaction->payment_status;
//                                    $shopOrder->status = 2;
//                                    ShopOrderDetail::where('order_id', $shopOrder->id)
//                                        ->update(['order_status' => 2, 'shipping_status' => 1]);
//                                } else {
//                                    $orderTotal->value = $orderTotal->value + $transaction_remaining;
//                                    $shopOrder->received = $shopOrder->received + $transaction_remaining;
//                                    $shopOrder->payment_status = 8;
//                                    $shopOrder->status = 8;
//                                    ShopOrderDetail::where('order_id', $shopOrder->id)
//                                        ->update(['order_status' => 8, 'shipping_status' => 1]);
//                                }
//                                $amount_receive = $shopOrder->received;
//                                $orderTotal->save();
//                            }
//                        }
//                    }
//                    $shopOrder->balance = $shopOrder->total - $shopOrder->received;
//                    $shopOrder->save();
//                    $oh_user_id = auth()->user()->id ?? 0;
//                    $other = array('admin_id' => $oh_user_id, 'order_detail_id' => 0, 'order_status' => 8, 'shipping_status' => 1);
//                    $content = 'Transaction No ' . $shopTransaction->transaction_id . ', Amount Received ' . $amount_receive . '';
//                    orderHistoryAdd($shopOrder->id, $content, $other);
//                } else {
//                    $shopOrder->status = $shopTransaction->payment_status;
//                    $shopOrder->payment_status = $shopTransaction->payment_status;
//                    $shopOrder->save();
//                    ShopOrderDetail::where('order_id', $shopOrder->id)
//                        ->update(['order_status' => $shopTransaction->payment_status, 'shipping_status' => 1]);
//
//                    $oh_user_id = auth()->user()->id ?? 0;
//                    $payment_status_text = ShopPaymentStatus::find($shopOrder->payment_status)->name ?? ' NA ';
//                    $content = 'Transaction No <span style="color:blue"> ' . $shopTransaction->transaction_id . '</span>, Payment Status <span style="color:red">"' . $payment_status_text . '"</span>, ' . $calling_from;
//                    $other = array('admin_id' => $oh_user_id, 'order_detail_id' => 0, 'order_status' => $shopOrder->status, 'shipping_status' => 1);
//                    orderHistoryAdd($shopOrder->id, $content, $other);
//                    Log::channel('payment')->info(__FILE__ . '| Line ' . __LINE__ . ' |Error shop_transactions payment_status is not 2 ' . ' |transaction_id ' . $transaction_id . '| payment_status ' . $shopTransaction->payment_status);
//                }
//            } else {
//                Log::channel('payment')->info(__FILE__ . '| Line ' . __LINE__ . ' |Error shop_orders not found ' . ' |transaction_id ' . $transaction_id . 'order ' . json_encode($order));
//            }
//        }
//    } else {
//        Log::channel('payment')->info(__FILE__ . '| Line ' . __LINE__ . ' |Error shop_transactions not found ' . ' |transaction_id ' . $transaction_id);
//    }
//}
//
//
//function completeOrderEcourier($orderDetailId = 0)
//{
//    $parcelDetails = [];
//    $comments = [];
//    $orderDetailIDs = [];
//    $shopOrderDetail = ShopOrderDetail::find($orderDetailId);
//    $merchant_id = '';
//    $shipping_charge = 0;
//    $number_of_iteam = 0;
//    $total_price = 0;
//    $total_product_price = 0;
//    if ($shopOrderDetail) {
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '| order details id' . $orderDetailId . '|order detail not found ');
//        return false;
//    }
//
//    $merchant_id = $shopOrderDetail->merchant_id;
//
//    $order_status_confirmed = config('order_status_confirmed');
//    $count = 0;
//    if ($shopOrderDetail->shipping_product_type == 1) {
//        $unchanged_order_status = config('unchanged_order_sttaus');
//        $unchanged_order_status_ids = explode(',', $unchanged_order_status);
//        $checkShippingAPICall = ShopOrderDetail::where('order_id', $shopOrderDetail->order_id)->where('merchant_id', $merchant_id)->whereIn('order_status', $unchanged_order_status_ids)->where('shipping_product_type', 1)->count();
//        if ($checkShippingAPICall >= 1) {
//            Log::info(__FILE__ . '| Line ' . __LINE__ . '| order details id' . $orderDetailId . '|order detail status not change "' . $checkShippingAPICall . '" Order');
//            return false;
//        }
//        $AllShopOrderDetails = ShopOrderDetail::where('order_id', $shopOrderDetail->order_id)->where('merchant_id', $merchant_id)->where('order_status', $order_status_confirmed)->where('shipping_product_type', 1)->get();
//
//        foreach ($AllShopOrderDetails as $value) {
//            if (strlen($value->delivery_reference_no) < 6) {
//                $count++;
//                $parcelDetails[] = ['name' => $value->name, 'sku' => $value->sku, 'qty' => $value->qty];
//                $comments[] = ['sku' => $value->sku, 'qty' => $value->qty];
//                $number_of_iteam = $number_of_iteam + $value->qty;
//                $total_price = $total_price + $value->total_price;
//                $shipping_charge = $value->shipping;
//                // if ($value->id != $orderDetailId) {
//                $orderDetailIDs[] = $value->id;
//                //  }
//            }
//        }
//    } else {
//        if ($order_status_confirmed == $shopOrderDetail->order_status && strlen($shopOrderDetail->delivery_reference_no) < 6) {
//            $count++;
//            $parcelDetails[] = ['name' => $shopOrderDetail->name, 'sku' => $shopOrderDetail->sku, 'qty' => $shopOrderDetail->qty];
//            $comments[] = ['sku' => $shopOrderDetail->sku, 'qty' => $shopOrderDetail->qty];
//            $number_of_iteam = $number_of_iteam + $shopOrderDetail->qty;
//            $total_price = $total_price + $shopOrderDetail->total_price;
//            $shipping_charge = $shopOrderDetail->shipping;
//            //  if ($shopOrderDetail->id != $orderDetailId) {
//            $orderDetailIDs[] = $shopOrderDetail->id;
//            //}
//        }
//    }
//    $total_product_price = $total_price;
//    $total_price = $total_price + $shipping_charge;
//    if ($count == 0) {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '| order details id' . $orderDetailId . '|order count "' . $count . '" ');
//        return false;
//    }
//    $shopOrder = ShopOrder::find($shopOrderDetail->order_id);
//    if ($shopOrder) {
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '| order details id: ' . $orderDetailId . '| Order ID: ' . $shopOrderDetail->order_id . '|order not found ');
//        return false;
//    }
//    $shopSetting = ShopSetting::where('merchant_id', '=', $shopOrderDetail->merchant_id)->first();
//    if ($shopSetting) {
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '| order details id: ' . $orderDetailId . '| Order ID: ' . $shopOrderDetail->order_id . '|shop setting ID:' . $shopOrderDetail->merchant_id . '|Shop Setting not found ');
//        return false;
//    }
//
//    $payment_method = "COD";
//    if ($shopOrder) {
//        if (isset($shopOrder->payment_method)) {
//            if (strtolower($shopOrder->payment_method) == 'cash') {
//                $payment_method = "COD";
//            } else if (strtolower($shopOrder->payment_method) == 'foster') {
//                $payment_method = "MPAY";
//                $total_price = 0;
//            } else if (strtolower($shopOrder->payment_method) == 'bkash') {
//                $payment_method = "MPAY";
//                $total_price = 0;
//            } else {
//                $payment_method = "MPAY";
//                $total_price = 0;
//            }
//        }
//    }
//    $package_code = $shopOrderDetail->delivery_package_code;
//    $curl = curl_init();
//    try {
//        $orderData = [
//            'order_code' => $shopOrder->id . '_' . $shopOrderDetail->id,
//            'recipient_name' => $shopOrder->customer_address->name ?? 'NA',
//            'recipient_mobile' => $shopOrder->customer_address->phone ?? 'NA',
//            'recipient_city' => urldecode($shopOrder->customer_address->city ?? 'NA'),
//
//            'recipient_district' => urldecode($shopOrder->customer_address->city ?? 'NA'),
//            'recipient_area' => urldecode($shopOrder->customer_address->area ?? 'NA'),
//            'recipient_thana' => urldecode($shopOrder->customer_address->thana ?? 'NA'),
//            'recipient_union' => $shopOrder->customer_address->post_code ?? 'NA',
//            'recipient_address' => urldecode($shopOrder->customer_address->address ?? 'NA'),
//            'package_code' => $package_code,
//            'product_price' => $total_price,
//            'actual_product_price' => $shopOrderDetail->total ?? 0,
//            'comments' => json_encode($comments),
//            'parcel_detail' => json_encode($parcelDetails),
//            'payment_method' => $payment_method,
//            'product_id' => $shopOrderDetail->variant_id,
//            'number_of_item' => $number_of_iteam,
//            'special_instruction' => $shopOrderDetail->order_note ?? '',
//            'ep_name' => $shopSetting->ep_name,
//            'ep_id' => $shopSetting->ep_id,
//            'pick_contact_person' => $shopSetting->pick_contact_person,
//            'pick_district' => $shopSetting->pick_district,
//            'pick_thana' => $shopSetting->pick_thana,
//            'pick_hub' => $shopSetting->pick_hub,
//            'pick_union' => $shopSetting->pick_post_code,
//            'pick_address' => $shopSetting->pick_address,
//            'pick_mobile' => $shopSetting->pick_mobile,
//        ];
//
//        $data = array(
//            CURLOPT_URL => config('shop.ecourier_api') . 'api/order-place-reseller',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => json_encode($orderData),
//            CURLOPT_HTTPHEADER => array(
//                "API-KEY:" . $shopSetting->merchant_api_key,
//                "API-SECRET:" . $shopSetting->merchant_api_secret,
//                "Content-Type: application/json",
//                "USER-ID:" . $shopSetting->merchant_user_id,
//                "cache-control: no-cache"
//            ),
//        );
//        Log::debug(__FILE__ . '| Line ' . __LINE__ . '|data ' . json_encode($data));
//    } catch (\Exception $ex) {
//        Log::error(__FILE__ . '| Line ' . __LINE__ . '| Error message: ' . json_encode($ex->getMessage()));
//        return false;
//    }
//    try {
//        curl_setopt_array($curl, $data);
//        $response = curl_exec($curl);
//        Log::debug(__FILE__ . '| Line ' . __LINE__ . '| response:' . $response);
//
//        $err = curl_error($curl);
//        curl_close($curl);
//        $eCourier_order_id = "";
//        if (empty($err)) {
//            $responseArray = json_decode($response);
//            $eCourier_order_id = isset($responseArray->ID) ? $responseArray->ID : '';
//            Log::info(__FILE__ . '| Line ' . __LINE__ . '| epmty error' . json_encode($response) . '| data' . json_encode($data));
//        }
//        if (empty($eCourier_order_id)) {
//            Log::info(__FILE__ . '| Line ' . __LINE__ . '| : eCourier_order_id not found' . json_encode($response) . '| data' . json_encode($data));
//            try {
//                $responseArray = json_decode($response);
//                if (isset($responseArray->errors))
//                    ShopOrderDetail::whereIn('id', $orderDetailIDs)->where('merchant_id', $merchant_id)
//                        ->update(['delivery_api_response' => substr(json_encode($responseArray->errors), 0, 1020)]);
//            } catch (\Exception $ex) {
//                Log::error(__FILE__ . '| Line ' . __LINE__ . '| Error message: ' . json_encode($ex->getMessage()));
//                return false;
//            }
//            return false;
//        }
//    } catch (\Exception $ex) {
//        Log::error(__FILE__ . '| Line ' . __LINE__ . '| Error message: ' . json_encode($ex->getMessage()));
//        return false;
//    }
////    $shopOrderDetail->delivery_reference_no = $eCourier_order_id;
////    $shopOrderDetail->shipping_status = 5;
////    $shopOrderDetail->save();
//    try {
//        $delivery_api_response = '';
//        if (isset($responseArray->message))
//            $delivery_api_response = substr(json_encode($responseArray->message), 0, 1020);
//        ShopOrderDetail::whereIn('id', $orderDetailIDs)->where('merchant_id', $merchant_id)
//            ->update(['delivery_reference_no' => $eCourier_order_id, 'shipping_status' => 5, 'delivery_api_response' => $delivery_api_response]);
//        $collection_charge = 0;
//        //  $total_price=$total_product_price+$shopOrderDetail->shipping;
//        if ($shopOrder->charge_area_type == 2 || $shopOrder->charge_area_type == '2') {
//            $collection_charge = ($total_price * (int)(config('collection_charge_percentage', 1))) / 100;
//        }
//        $orderShippingDetails = new ShopOrderShippingDetail();
//        $orderShippingDetails->merchant_id = $merchant_id;
//        $orderShippingDetails->order_id = $shopOrderDetail->order_id;
//        $orderShippingDetails->product_shipping_type = $shopOrderDetail->shipping_product_type;
//        $orderShippingDetails->shipping_charge = $shopOrderDetail->shipping;
//        $orderShippingDetails->total_product_price = $total_product_price;
//        $orderShippingDetails->collection_amount = $total_price;
//        $orderShippingDetails->collection_charge = $collection_charge;
//        $orderShippingDetails->gift_wrap_charge = $shopOrderDetail->gift_wrap_charge;
//        $orderShippingDetails->fragile_charge = $shopOrderDetail->fragile_charge;
//        $orderShippingDetails->delivery_package_code = $shopOrderDetail->delivery_package_code;
//        $orderShippingDetails->delivery_reference_no = $eCourier_order_id;
//        $orderShippingDetails->shipping_status = 5;
//        $orderShippingDetails->delivery_api_response = $delivery_api_response;
//        $orderShippingDetails->courier_payment_status =(int)config('courier_payment_status_draft');
//        $orderShippingDetails->save();
//        ShopOrderDetail::whereIn('id', $orderDetailIDs)->where('merchant_id', $merchant_id)
//            ->update(['order_shipping_detail_id' => $orderShippingDetails->id]);
//    } catch (\Exception $ex) {
//        Log::error(__FILE__ . '| Line ' . __LINE__ . '| Error message: ' . json_encode($ex->getMessage()));
//        return false;
//    }
//
//    return true;
//}

//
//if (!function_exists('get_merchant_id')) {
//
//    function get_merchant_id($admin_user_id)
//    {
//        $user_id = Administrator::find($admin_user_id);
//        if ($user_id->isRole('merchant')) {
//            return $user_id->id;
//        } elseif ($user_id->isRole('admin')) {
//            return 0;
//        } elseif ($user_id->isRole('administrator')) {
//            return 0;
//        } elseif ($user_id->isRole('sub-merchant')) {
//            return $user_id->id;
//        } else {
//            return $user_id->parent_id;
//        }
//    }
//
//}
//
//
//function get_ref_info_detail_by_agent_token($agent_token)
//{
//    $data = ['register_source' => 'NA', 'agent_token' => $agent_token, 'name' => 'Invalid agent Token'];
//    try {
//        $response_re_agent['data'] = array();
//        $response_re_agent = \App\Repositories\ReferralEngine::agentInfo(['access_token' => config('default_merchant_access_token'), 'agent_token' => $agent_token]);
//
//        if ($response_re_agent['code'] == '200' || $response_re_agent['code'] == 200) {
//            $data = $response_re_agent['data'];
//        } else {
//            Log::info(__FILE__ . '| Line ' . __LINE__ . '| get_ref_info_detail_by_agent_token response data not found : ' . json_encode($response_re_agent));
//        }
//    } catch (\Exception $ex) {
//        Log::error(__FILE__ . '| Line ' . __LINE__ . '| Error message: ' . json_encode($ex->getMessage()));
//    }
//    return $data;
//}

function shop_make_title($data)
{
    $data = \Illuminate\Support\Str::title($data);
    return str_replace("_", " ", $data);
}

//
//function get_sub_merchant_list()
//{
//    $merchants = Administrator::select('id', 'name')
//        ->where('parent_id', '=', config('default_merchnat_id', 3))
//        ->whereHas('roles', function (Builder $query) {
//            $query->where('role_id', '=', config('default_sub_merchnat_roll_id', 5));
//        })
//        ->orderBy('name', 'asc')
//        ->pluck('name', 'id');
//    return $merchants;
//}
//
//function get_payment_log($transaction_id, $payment_method)
//{
//    $return_response = array();
//    $table_payment_log = 'shop_ex_' . strtolower($payment_method) . '_log';
//    $table_payment_ipn_log = 'shop_ex_ipn_log';
//    $return_response2 = array();
//    if (Schema::hasTable($table_payment_log)) {
//        $return_response = DB::table($table_payment_log)
//            ->select('transaction_id', 'details', 'created_at')
//            ->where('transaction_id', '=', $transaction_id);
//
//        if (Schema::hasTable($table_payment_ipn_log)) {
//            $return_response2 = DB::table($table_payment_ipn_log)
//                ->select('transaction_id', 'details', 'created_at')
//                ->where('transaction_id', '=', $transaction_id)
//                ->union($return_response)
//                ->get();
//        } else {
//            $return_response2 = $return_response->get();
//        }
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '|  Payment log not found ' . json_encode($table_payment_log));
//    }
//    return $return_response2;
//}
//
//function remove_payment_gateway_key($data)
//{
//    $data = str_replace('mcnt_AccessCode', '', $data);
//    $data = str_replace('mcnt_ShortName', '', $data);
//    $data = str_replace('mcnt_ShopId', '', $data);
//    $data = str_replace('hashkey', '', $data);
//    $data = str_replace('mcnt_SecureHashValue', '', $data);
//    return $data;
//}
//
//
//function get_order_sms_log($order_id)
//{
//    $return_response = array();
//    $order_sms_log = 'shop_sms_logs';
//    if (Schema::hasTable($order_sms_log)) {
//        $return_response = DB::table($order_sms_log)
//            ->select('status', 'mobile_no', 'message', 'message_id', 'status_message', 'created_at')
//            ->where('order_id', '=', $order_id)
//            ->get();
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '|  order_sms_log not found ' . json_encode($order_sms_log));
//    }
//    return $return_response;
//}
//
//function get_agent_token($order_id)
//{
//    $return_response = array();
//    $info_table = 'shop_order_infos';
//    if (Schema::hasTable($info_table)) {
//        $return_response = DB::table($info_table)
//            //->select('info_value')
//            ->where('info_key', '=', 'agent_token')
//            ->where('order_id', '=', $order_id)
//            ->first()// ->toArray()
//        ;
//    } else {
//        Log::info(__FILE__ . '| Line ' . __LINE__ . '|  agent_token not found ' . json_encode($info_table));
//    }
//    return $return_response->info_value;
//}
//
//function get_variant_status_by_attribute_detail($attribute_details_id, $attribute_group_id = 1, $cbs_product_id = 0, $stock = 0)
//{
//    $status = 0;
//    $variant = ShopProductVariant::where('attribute_details_id_' . $attribute_group_id, '=', $attribute_details_id)->where('status', '=', 1)->where('cbs_product_id', '=', $cbs_product_id);//where('stock', '>', 0)->
//    if ($stock == 0) {
//        $variant = $variant->where('stock', '>', 0);
//    } else {
//        $variant = $variant->where('stock', '<=', 0);
//    }
//    $variant = $variant->first();
//    if ($variant) {
//        $status = $variant->status;
//    } else {
//        // Log::info(__FILE__ . '| Line ' . __LINE__ . '|  attribute_details_id ' . $attribute_details_id . ' | status' . $status);
//    }
//  //  Log::info(__FILE__ . '| Line ' . __LINE__ . '|  attribute_details_id ' . $attribute_details_id . ' | status' . $status . ' | $cbs_product_id' . $cbs_product_id);
//    return $status;
//}

/**
 * @return mixed
 */
function getIPAddress()
{
    //whether ip is from the share internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } //whether ip is from the proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } //whether ip is from the remote address
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getLocationByIP($ip = '')
{
    $ip_data = @json_decode(file_get_contents(config('ip_info_url', 'https://ipinfo.dotlines.com.sg/api/ip-lookup?ip=') . $ip . ""));
    $response_data = 'BD';
    if (@$ip_data->status == '200' || @$ip_data->status == 200) {
        foreach (@$ip_data->data as $val) {
            $response_data = $val->country;
            break;
        }
    } else {
        Log::info(__FILE__ . '| Line ' . __LINE__ . '| Location Not found $ip_data ' . json_encode($ip_data) . ' | IP' . $ip);
    }
    return $response_data;
}

//
//function getComissionByReferenceID($ref=""){
//    try{
//        //Log::info($ref);
//        $comission = DB::table('re_earn_commissions')->where('reference_id','=',$ref)->first();
//        //Log::info(json_encode($comission));
//        if(empty($comission)){
//            return 0;
//        }
//        return $comission->commission;
//
//    } catch (Exception $ex) {
//        Log::error(__FILE__ . ' Line No ' . __LINE__ . '| ' . 'comission not found for '.$ref. "   " . $ex->getMessage() . ' data ' . json_encode($ex));
//        return 0;
//    }
//}

function getPaymentGetway()
{
    $modulePayment = getExtensionsGroup('payment');
    $sourcesPayment = classNames('Extensions', 'payment');
    $paymentMethod = array();
    foreach ($modulePayment as $key => $module) {
        // dd($module);
        if (in_array($module['key'], $sourcesPayment)) {
            $moduleClass = 'App\Extensions\Payment\Controllers\\' . $module['key'];
            $paymentMethod[] = (new $moduleClass)->getData();
            //   echo $moduleClass;
        }
    }
    return $paymentMethod;
}

function getPaymentGetwayTextOnly()
{
    $modulePayment = getExtensionsGroup('payment');
    $sourcesPayment = classNames('Extensions', 'payment');
    $paymentMethod = array();
    foreach ($modulePayment as $key => $module) {
        // dd($module);
        if (in_array($module['key'], $sourcesPayment)) {
            $moduleClass = 'App\Extensions\Payment\Controllers\\' . $module['key'];
            $paymentMethod[(new $moduleClass)->getData()['code']] = (new $moduleClass)->getData()['code'];
            //   echo $moduleClass;
        }
    }
    return $paymentMethod;
}

function order_history($order_id, $content, $upi)
{
    $GcoOrderHistory = new GcoOrderHistory();
    try {
        $admin_id = Admin::user()->id ?? 0;
        $GcoOrderHistory->order_id = $order_id;
        $GcoOrderHistory->content = $content;
        $GcoOrderHistory->upi = $upi;
        $GcoOrderHistory->cb = $admin_id;
        $GcoOrderHistory->save();
    } catch (Exception $ex) {
        Log::error(__FILE__ . ' Line No ' . __LINE__ . '| ' . $ex->getMessage());
    }
    return $GcoOrderHistory;
}

function display_amount($amount)
{
    $decimals = config('display_amount_decimals', 2);
    $decimals = intval($decimals);
    $amount = number_format((float)$amount, $decimals, '.', '');
    return $amount . ' ' . config('desplay_amount_prefix', 'TK');
}

function order_process($id)
{
    $gcoOrder = GcoOrder::with('details')->with('order_total')->with('order_source_app_info')->with('order_status')->with('payment_status')->with('shipping_status')->with('history')->where('id', $id)->first();
    if ($gcoOrder->payment_status_id == config('payment_status_paid', 6) || $gcoOrder->payment_status_id == config('payment_status_complete', 4)) {
        $gcoOrder->received = $gcoOrder->total;
        $gcoOrder->balance = $gcoOrder->balance - $gcoOrder->received;
        $gcoOrder->save();
        $gcoOrderTotal = \App\Models\Gco\GcoOrderTotal::where('order_id', '=', $id)->first();
        $gcoOrderTotal->received = $gcoOrder->received;
        $gcoOrderTotal->balance = $gcoOrder->balance;
        $gcoOrderTotal->save();
    } else {
        Log::info(__FILE__ . '|' . __LINE__ . '| payment status' . $gcoOrder->payment_status_id);
    }
}

function investor_date_show($date)
{
    return date('F d, Y', strtotime($date));
}

function clean_string($string)
{
    try {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $result = explode('-', $string);
        $result = array_filter($result);
        $string = implode('-', $result);
        //Log::info('||' . json_encode(Str::kebab($string)));
        $string = Str::kebab($string);
    } catch (Exception $ex) {
        Log::error($ex->getMessage());
    }
    return $string;
}

function config_to_number($key, $default = 0)
{
    $value = config($key, $default);
    return intval($value);
}

//use GuzzleHttp\Client;
//use GuzzleHttp\Psr7\Request;
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

function get_service_list_from_his2()
{
    $client = new \GuzzleHttp\Client();
    $request = new \GuzzleHttp\Psr7\Request('GET', config('application.ept_service_list_from_his'));
    $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYPEER, false);
    $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYHOST, false);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody();
}

function get_service_list_from_his()
{
    $response = [];
    try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('application.ept_service_list_from_his'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        Log::error(json_encode($response));
    } catch (Exception $ex) {
        Log::error($ex->getMessage());
    }
    return $response;

}

function add_extra_parameter()
{
    return http_build_query(['param1' => 'value', 'param2' => 'value']);
}
