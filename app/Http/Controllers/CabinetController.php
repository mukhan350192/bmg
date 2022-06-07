<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CabinetController extends Controller
{
    public function login(Request $request){
        $iin = $request->input('iin');
        $password = $request->input('password');
        $result['success'] = false;
        do {
            if (!$iin){
                $result['message'] = 'Не передан логин';
                break;
            }
            if (!$password){
                $result['message'] = 'Не передан пароль';
                break;
            }
            $user = User::where('iin',$iin)->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            if (Hash::check($password,$user->password)){
                $result['message'] = 'Логин или пароль неправильный';
                break;
            }
            $token = sha1(Str::random(64).time());
            $user->token = $token;
            $user->save();
            $result['success'] = true;
            $result['token'] = $token;
        }while(false);
        return response()->json($result);
    }

    public function loginTest(Request $request){
        $iin = $request->input('iin');
        $password = $request->input('password');
        $result['success'] = false;
        do{
            if (!$iin){
                break;
            }
            if (!$password){
                break;
            }
            $user = User::where('iin',$iin)->first();
            if (!$user){
                break;
            }
            print_r($user->password);
            var_dump(Hash::check($password,$user->password));
            if (Hash::check($password,$user->password)){
                $result['message'] = true;
            }else{
                $result['message'] = false;
            }
        }while(false);
        return response()->json($result);
    }

    public function getUserInfo(Request $request){
        $token = $request->input('token');
        $result['success'] = false;
        do{
            if (!$token){
                $result['message'] = 'Пользователь не авторизован';
                break;
            }
            $user = User::where('token',$token)->select('iin')->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $http = new Client(['verify'=>false]);
            try{
                $response = $http->get('https://icredit-crm.kz/api/webhock/personalCabinet.php',[
                    'query' => [
                        'iin' => $user->iin,
                    ]
                ]);
                return $response->getBody()->getContents();
            }catch (BadResponseException $e){
                info('cannot get UserData');
            }
        }while(false);
        return response()->json($result);
    }

    public function getUserHistory(Request $request){
        $token = $request->input('token');
        $result['success'] = false;
        do{
            if (!$token){
                $result['message'] = 'Пользователь не авторизован';
                break;
            }
            $user = User::where('token',$token)->select('iin')->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $http = new Client(['verify'=>false]);
            try{
                $response = $http->get('https://icredit-crm.kz/api/webhock/history.php',[
                    'query' => [
                        'iin' => $user->iin,
                    ]
                ]);
                return $response->getBody()->getContents();
            }catch (BadResponseException $e){
                info('cannot get UserData');
            }
        }while(false);
        return response()->json($result);
    }

    public function getUserProfileFromBitrix(Request $request)
    {
        $token = $request->input('token');
        $result['success'] = false;
        $key = 'MSBckXf5530ZlFQIgHPeJYsF4mE8FjUX';
        do {
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }
            $user = User::where('token',$token)->select('iin')->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $key = 'MSBckXf5530ZlFQIgHPeJYsF4mE8FjUX';

            $http = new Client(['verify' => false]);
            try {
                $response = $http->get('https://icredit-crm.kz/api/site/request.php', [

                    'query' => [
                        'iin' => $user->iin,
                        'key' => $key,
                    ],
                ]);

                return $response->getBody()->getContents();

            } catch (BadResponseException $e) {
                info('cannot get userData');
            }
        } while (false);
        return response()->json($result);
    }

    public function notFull(Request $request){
        $token = $request->input('token');
        $result['success'] = false;
        do {
            if (!$token) {
                $result['message'] = 'Не передан иин';
                break;
            }
            $user = User::where('token',$token)->select('iin')->first();
            if (!$user){
                $result['message'] = 'Пользователь не найден';
                break;
            }
            $iin = $user->iin;
            $url = 'https://icredit-crm.kz/api/site/not_full_anketa.php?iin=' . $iin;
            $s = file_get_contents($url);
            $s = json_decode($s, true);
            if (isset($s) && $s['step'] == 2) {
                $result['step'] = 2;
                $result['success'] = true;
                break;
            }
            if (isset($s) && $s['step'] == 3) {
                $result['step'] = 3;
                $result['success'] = true;
                break;
            }
            if (isset($s) && $s['step'] == 1) {
                $result['step'] = 1;
                $result['success'] = true;
                break;
            }
        } while (false);
        return response()->json($result);
    }

    public function getRepeatRequest(Request $request)
    {
        $token = $request->input('token');
        $key = 'MSBckXf5530ZlFQIgHPeJYsF4mE8FjUX';
        $result['success'] = false;
        do {
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }
            $user = User::where('token',$token)->select('iin')->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $http = new Client(['verify' => false]);
            try {
                $response = $http->get("http://icredit-crm.kz/api/site/repeat.php", [

                    'query' => [
                        'iin' => $user->iin,
                        'key' => $key,
                    ],
                ]);

                $xml =  $response->getBody()->getContents();
                $res = json_decode($xml, true);
                if (isset($res['success']) && $res['success'] == false){
                    if (isset($res['message'])){
                        $result['message'] = $res['message'];
                        break;
                    }
                    if (isset($res['date'])){
                        $result['message'] = 'Вам пока отказано подавать повторный займ до '.$res['date'];
                        break;
                    }
                }
                $result['success'] = true;

            } catch (BadResponseException $e) {
                info($e);
            }

        } while (false);
        return response()->json($result);
    }

    public function repeatRequest(Request $request){
        $token = $request->input('token');
        $period = $request->input('period');
        $amount = $request->input('amount');
        $result['success'] = false;
        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            if (!$period){
                $result['message'] = 'Не передан срок';
                break;
            }
            if (!$amount){
                $result['message'] = 'Не передан сумма';
                break;
            }
            $user = User::where('token',$token)->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $http = new Client(['verify' => false]);
            try {
                $response = $http->get('https://icredit-crm.kz/api/webhock/repeat.php', [

                    'query' => [
                        'iin' => $user->iin,
                        'period' => $period,
                        'amount' => $amount,
                    ],
                ]);

                return $response->getBody()->getContents();

            } catch (BadResponseException $e) {
                info('cannot get userData');
            }

        }while(false);
        return response()->json($result);
    }
}
