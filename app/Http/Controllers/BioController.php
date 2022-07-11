<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BioController extends Controller
{
    public function takeCode(Request $request)
    {
        $phone = $request->input('phone');
        $iin = $request->input('iin');
        $result['success'] = false;
        do {
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            if (!$iin) {
                $result['message'] = 'Не передан иин';
                break;
            }
            $user = User::where('iin',$iin)->first();
            if ($user){
                $result['message'] = 'Пользователь существует';
                break;
            }
            $http = new Client(['verify' => false]);


            $url = "http://178.170.221.75/biometria/public/api/takeCode?iin=$iin&phone=$phone";
            $response = $http->get($url);
            $status = $response->getStatusCode();
            $response = $response->getBody()->getContents();
            $response = json_decode($response,true);
            if ($status == 500) {
                $result['message'] = '500';
                break;
            }
            if (isset($response['success']) && $response['success'] == true) {
                $result['success'] = true;
                break;
            }
            if (isset($response['success']) && $response['success'] == false) {
                $result['success'] = false;
                break;
            }
        } while (false);

        return response()->json($result);
    }

    public function sendSMS(Request $request)
    {
        $phone = $request->input('phone');
        $source = $request->input('source');
        $result['success'] = false;
        do {
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            if (strlen($phone) != 11) {
                $result['message'] = 'Некорректный формат телефона';
                break;
            }
            if (!$source) {
                $result['message'] = 'Не передан откуда';
                break;
            }
            $code = rand(1000, 9999);
            DB::beginTransaction();
            $codeInsert = DB::table('sms_code')->insertGetId([
                'phone' => $phone,
                'code' => $code,
            ]);
            $http = new Client(['verify' => false]);
            $http->get('https://sms.i-credit.kz/api/typeOne',[
                'query' => [
                    'code' => $code,
                    'phone' => $phone,
                    'source' => $source,
                ]
            ]);
            if (!$codeInsert) {
                $result['message'] = 'Попробуйте позже';
                DB::rollBack();
                break;
            }
            DB::commit();
            $result['code'] = $code;
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function confirmSMS(Request $request)
    {
        $code = $request->input('code');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $fatherName = $request->input('fatherName');
        $iin = $request->input('iin');
        $password = $request->input('password');
        $amount = $request->input('amount');
        $period = $request->input('period');
        $phone = $request->input('phone');
        $docNumber = $request->input('docNumber');
        $docIssue = $request->input('docIssue');
        $startGiven = $request->input('startGiven');
        $endGiven = $request->input('endGiven');
        $email = $request->input('email');
        $source = $request->input('source');
        $utm_medium = $request->input('utm_medium');
        $utm_campaign = $request->input('utm_campaign');
        $utm_content = $request->input('utm_content');
        $result['success'] = false;
        do {
            if (!$code) {
                $result['message'] = 'Не передан код';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            if (strlen($code) == 4){
                $findCode = DB::table('sms_code')->where('code', $code)->where('phone', $phone)->first();
                if (!$findCode) {
                    $result['message'] = 'Не совпадает код';
                    break;
                }
                $bmg = 2;
            }else{
                $bmg = 1;
            }

            if (!$name) {
                $result['message'] = 'Не передан имя';
                break;
            }
            if (!$surname) {
                $result['message'] = 'Не передан фамилия';
                break;
            }
            if (!$iin) {
                $result['message'] = 'Не передан ИИН';
                break;
            }
            if (!$password) {
                $result['message'] = 'Не передан пароль';
                break;
            }
            $user = User::where('iin', $iin)->first();
            if ($user) {
                $result['message'] = 'Пользователь зарегистирован';
                break;
            }
            if (!$amount) {
                $result['message'] = 'Не передан сумма';
                break;
            }
            if (!$period) {
                $result['message'] = 'Не передан срок';
                break;
            }
            if (!$docNumber) {
                $result['message'] = 'Не передан номер документа';
                break;
            }
            if (!$docIssue) {
                $result['message'] = 'Не передан орган выдачи';
                break;
            }
            if (!$startGiven) {
                $result['message'] = 'Не передан номер документа';
                break;
            }
            if (!$endGiven) {
                $result['message'] = 'Не передан номер документа';
                break;
            }
            if (!$email) {
                $result['message'] = 'Не передан почта';
                break;
            }
            if (!$source){
                $result['message'] = 'Не передан источник';
                break;
            }
            $token = sha1(Str::random(64) . time());
            DB::beginTransaction();
            $newUser = User::create([
                'iin' => $iin,
                'password' => bcrypt($password),
                'phone' => $phone,
                'token' => $token,
                'name' => $name,
                'surname' => $surname,
                'fatherName' => $fatherName,
                'email' => $email,
                'docNumber' => $docNumber,
                'docIssue' => $docIssue,
                'startGiven' => $startGiven,
                'endGiven' => $endGiven,
            ]);
            if (!$newUser) {
                $result['message'] = 'Попробуйте позже';
                DB::rollBack();
                break;
            }
            DB::commit();
            $http = new Client(['verify' => false]);

            $link = 'https://icredit-crm.kz/api/site/bmg_newStep1.php';

            $response = $http->get($link, [
                'query' => [
                    'iin' => $iin,
                    'password' => $password,
                    'name' => $name,
                    'surname' => $surname,
                    'fatherName' => $fatherName,
                    'amount' => $amount,
                    'period' => $period,
                    'phone' => $phone,
                    'docNumber' => $docNumber,
                    'docIssue' => $docIssue,
                    'startGiven' => $startGiven,
                    'endGiven' => $endGiven,
                    'email' => $email,
                    'code' => $code,
                    'ID' => time(),
                    'source' => $source,
                    'bmg' => $bmg,
                    'utm_medium' => $utm_medium,
                    'utm_campaign' => $utm_campaign,
                    'utm_content' => $utm_content,
                ],
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $leadID = $response['leadID'];
            User::where('id',$newUser->id)->update(['leadID'=>$leadID]);
            $result['success'] = true;
            $result['leadID'] = $leadID;
            $result['token'] = $token;
        } while (false);
        return response()->json($result);
    }

    public function secondStep(Request $request)
    {
        $sphere = $request->input('sphere');
        $workPlace = $request->input('workPlace');
        $position = $request->input('position');
        $lastSix = $request->input('lastSix');
        $deposit = $request->input('deposit');
        $fioContact = $request->input('fioContact');
        $phoneContact = $request->input('phoneContact');
        $relativeContact = $request->input('relativeContact');
        $source = $request->input('source');
        $token = $request->input('token');

        $result['success'] = false;
        do {
            if (!$sphere) {
                $result['message'] = 'Не передан сфера работы';
                break;
            }
            if (!$position) {
                $result['message'] = 'Не передан должность';
                break;
            }

            if (!$fioContact) {
                $result['message'] = 'Не передан ФИО родственника';
                break;
            }
            if (!$phoneContact) {
                $result['message'] = 'Не передан телефон контакта';
                break;
            }
            if (!$relativeContact){
                $result['message'] = 'Не передан кем приходиться';
                break;
            }
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }

            $user = User::where('token', $token)->first();
            if (!$user) {
                $result['message'] = 'Пользователь не найден';
                break;
            }
            DB::beginTransaction();
            $user_work = DB::table('user_work')->insertGetId([
                'sphere' => $sphere,
                'position' => $position,
                'workPlace' => $workPlace,
                'lastSix' => $lastSix,
                'deposit' => $deposit,
                'fioContact' => $fioContact,
                'phoneContact' => $phoneContact,
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            if (!$user_work) {
                $result['message'] = 'Попробуйте позже';
                DB::rollBack();
                break;
            }
            $http = new Client(['verify' => false]);

            $link = 'https://icredit-crm.kz/api/site/bmg_newStep2.php';

            $response = $http->get($link, [
                'query' => [
                    'leadID' => $user->leadID,
                    'sphere' => $sphere,
                    'workPlace' => $workPlace,
                    'position' => $position,
                    'lastSix' => $lastSix,
                    'deposit' => $deposit,
                    'source' => $source,
                    'contactOne' => $fioContact,
                    'phoneOne' => $phoneContact,
                    'relativeOne' => $relativeContact,
                ],
            ]);
            DB::commit();

            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function thirdStep(Request $request)
    {
        $token = $request->input('token');
        $iban = $request->input('iban');
        $cardNumber = $request->input('cardNumber');
        $cardIssue = $request->input('cardIssue');
        $cardName = $request->input('cardName');
        $source = $request->input('source');
        $clickID = $request->input('clickID');
        $webID = $request->input('web_id');
        $result['success'] = false;
        do {
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }
            if (!$iban) {
                $result['message'] = 'Не передан IBAN';
                break;
            }
            if (!$cardNumber) {
                $result['message'] = 'Не передан номер карты';
                break;
            }
            if (!$cardIssue) {
                $result['message'] = 'Не передан срок действие карты';
                break;
            }
            if (!$cardName) {
                $result['message'] = 'Не передан имя владельца';
                break;
            }
            $user = User::where('token', $token)->first();
            if (!$user) {
                $result['message'] = 'Не найден пользователь';
                break;
            }
            DB::beginTransaction();
            $user_card = DB::table('user_card')->insertGetId([
                'iban' => $iban,
                'cardNumber' => $cardNumber,
                'cardName' => $cardName,
                'cardIssue' => $cardIssue,
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            if (!$user_card) {
                $result['message'] = 'Попробуйте позже';
                DB::rollBack();
                break;
            }
            $http = new Client(['verify' => false]);

            $link = 'https://icredit-crm.kz/api/site/bmg_newStep3.php';

            $response = $http->get($link, [
                'query' => [
                    'leadID' => $user->leadID,
                    'source' => $source,
                    'clickID' => $clickID,
                    'iban' => $iban,
                    'cardNumber' => $cardNumber,
                    'cardIssue' => $cardIssue,
                    'cardName' => $cardName,
                    'web_id' => $webID,
                ],
            ]);
            DB::commit();
            $ID = strtotime($user->created_at);
            //$this->cpaPostback($source,$clickID,$ID);
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function test(Request $request)
    {
        $iin = $request->input('iin');
        $birth = str_split($iin, 1);
        if ($birth[6] == 3) {
            $gender = 'M';
            $birthday = $birth[4] . $birth[5] . '.' . $birth[2] . $birth[3] . '.' . '19' . $birth[0] . $birth[1];
        }
        if ($birth[6] == 4) {
            $gender = 'F';
            $birthday = $birth[4] . $birth[5] . '.' . $birth[2] . $birth[3] . '.' . '19' . $birth[0] . $birth[1];
        }
        if ($birth[6] == 5) {
            $gender = 'M';
            $birthday = $birth[4] . $birth[5] . '.' . $birth[2] . $birth[3] . '.' . '20' . $birth[0] . $birth[1];
        }
        if ($birth[6] == 6) {
            $gender = 'F';
            $birthday = $birth[4] . $birth[5] . '.' . $birth[2] . $birth[3] . '.' . '20' . $birth[0] . $birth[1];
        }
        echo $gender . " " . $birthday;
    }
}
