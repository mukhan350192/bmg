<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PayboxController extends Controller
{
    public function payment(Request $request){
        $rules = array(
            'amount' => 'required',
            'iin' => 'required'
        );
        $messages = [
            'amount.required' => 'Требуется ввести сумму',
            'iin.required' => 'Пользователь не найден',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $amount = $request->input('amount');
        $user_id = $request->input('iin');
        $success_url = 'i-credit.kz/cabinet?success=true';
        $failure_url = 'i-credit.kz/cabinet?false=true';
        $merchant_id = 517822;

        $description = 'Погашение займа';

        $url = 'https://api.paybox.money/payment.php';

        $data = [
            'extra_user_id' => $user_id,
            'pg_merchant_id' => 517822,//our id in Paybox, will be gived on contract
            'pg_amount' => $amount, //amount of payment
            'pg_salt' => "Salt", //amount of payment
            'pg_order_id' => $user_id, //id of purchase, strictly unique
            'pg_description' => $description, //will be shown to client in process of payment, required
            'pg_result_url' => route('payment-result'),//route('payment-result')
            'pg_success_url' => $success_url,
        ];

        ksort($data);
        array_unshift($data, 'payment.php');
        array_push($data, 'vKuygqBoLgE7dxDp');

        $data['pg_sig'] = md5(implode(';', $data));

        unset($data[0], $data[1]);

        $query = http_build_query($data);
        $arr = [$url, $query];
        return $arr;
    }

    public function paymentResult(Request $request){
        if ($request->pg_result) {

            DB::table('payments')->insertGetId([
               'iin' => $request->extra_user_id,
               'amount' => $request->pg_amount,
               'phone' => $request->pg_user_phone,
               'order_id' => $request->order_id,
               'payment_id' => $request->payment_id,
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now(),
            ]);
            $iin = $request->extra_user_id;
            $amount = $request->pg_amount;
            date_default_timezone_set('Asia/Almaty');
            $time = date('d.m.Y H:i:s');
            $http = new Client(['verify' => false]);

            $link = 'http://37.18.30.111/api/payBox';
            try {
                $response = $http->get($link, [

                    'query' => [
                        'amount' => $amount,
                        'iin' => $iin,
                        'time' => $time,
                        'phone' => $request->pg_user_phone,
                    ]
                ]);
                info("paybox payment " . $response->getBody());
            } catch (BadResponseException $e) {
                info('Bad request ' . $e->getCode());
            }
            return response()->json([
                'message' => 'ok',
                'success' => true
            ])->setStatusCode(200);

        }
        return response()->json([
            'message' => 'fail in life',
            'success' => false
        ])->setStatusCode(400);
    }
}
