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
            if (!Hash::check($password,$user->password)){
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
}
