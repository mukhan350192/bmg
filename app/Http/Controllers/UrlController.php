<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function getShortUrl(Request $request)
    {
        $leadID = $request->input('leadID');
        $main = $request->input('main');
        $period = $request->input('period');
        $amountLast = $request->input('amountLast');
        $fio = $request->input('fio');
        $iin = $request->input('iin');
        $address = $request->input('address');
        $cardGiven = $request->input('cardGiven');
        $cardDate = $request->input('cardDate');
        $cardExpiration = $request->input('cardExpiration');
        $phone = $request->input('phone');
        $iban = $request->input('iban');
        $email = $request->input('email');
        $repaymentDate = $request->input('repaymentDate');
        $repaymentAmount = $request->input('repaymentAmount');
        $reward = $request->input('reward');
        $contractNumber = $request->input('contractNumber');
        $insuranceAmount = $request->input('insuranceAmount');
        $code = $request->input('code');
        $birthday = $request->input('birthday');
        $work = $request->input('work');
        $position = $request->input('position');
        $placeOfBirth = $request->input('placeOfBirth');
        $cardNumber = $request->input('cardNumber');
        $token = Str::random(16);
        $result['success'] = false;
        do {
            $s = DB::table('new_short_url')->insertGetId([
                'token' => $token,
                'leadID' => $leadID,
                'main' => $main,
                'period' => $period,
                'amountLast' => $amountLast,
                'fio' => $fio,
                'iin' => $iin,
                'address' => $address,
                'cardGiven' => $cardGiven,
                'cardNumber' => $cardNumber,
                'cardDate' => $cardDate,
                'cardExpiration' => $cardExpiration,
                'phone' => $phone,
                'iban' => trim($iban),
                'email' => $email,
                'repaymentDate' => $repaymentDate,
                'repaymentAmount' => $repaymentAmount,
                'reward' => $reward,
                'contractNumber' => $contractNumber,
                'insuranceAmount' => $insuranceAmount,
                'code' => $code,
                'birthday' => $birthday,
                'work' => $work,
                'position' => $position,
                'placeOfBirth' => $placeOfBirth,
                'givenDate' => $request->input('givenDate'),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $url = "https://i-credit.kz/newAggrements?token=$token&rest=false";
            $result['success'] = true;
            $result['url'] = $url;
        } while (false);
        return response()->json($result);
    }


    /**
     * @OA\Get(
     ** path="/api/newGetData",
     *   tags={"Подпись по СМС"},
     *   summary="Получение данные при подписание",
     *
     *  @OA\Parameter(
     *      name="token",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="leadID",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="true/false",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/

    public function newGetData(Request $request)
    {
        $token = $request->input('token');
        $leadID = $request->input('leadID');
        $result['success'] = true;

        do {
            if (!$token && !$leadID) {
                $result['message'] = 'Не передан токен!';
                break;
            }
            if ($token) {
                $get = DB::table('new_short_url')
                    ->select('*')
                    ->where('token', $token)
                    ->where('status', 1)
                    ->whereDate('created_at', '>=', now()->subDays(3)->setTime(0, 0, 0)->toDateTimeString())
                    ->first();
            }
            if ($leadID) {
                $get = DB::table('new_short_url')
                    ->select('*')
                    ->where('leadID', $leadID)
                    ->where('status', 1)
                    ->whereDate('created_at', '>=', now()->subDays(3)->setTime(0, 0, 0)->toDateTimeString())
                    ->first();
            }


            if (!isset($get->id)) {
                $result['success'] = false;
                $result['message'] = 'По токену не было найдено документов.';
                break;
            }

            $result['leadID'] = $get->leadID;
            $result['main'] = $get->main;
            $result['period'] = $get->period;
            $result['amountLast'] = $get->amountLast;
            $result['fio'] = $get->fio;
            $result['address'] = $get->address;
            $result['iin'] = $get->iin;
            $result['cardGiven'] = $get->cardGiven;
            $result['cardDate'] = $get->cardDate;
            $result['cardExpiration'] = $get->cardExpiration;
            $result['phone'] = $get->phone;
            $result['iban'] = $get->iban;
            $result['email'] = $get->email;
            $result['datePayment'] = date('d.m.Y', strtotime($get->repaymentDate));
            $result['givenDate'] = date('d.m.Y', strtotime($get->givenDate));
            $result['total'] = $get->repaymentAmount;
            $result['reward'] = $get->reward;
            $result['contractNumber'] = $get->contractNumber;
            $result['insuranceAmount'] = $get->insuranceAmount;
            $result['code'] = $get->code;
            $result['birthday'] = $get->birthday;
            $result['work'] = $get->work;
            $result['position'] = $get->position;
            $result['placeOfBirth'] = $get->placeOfBirth;
            $result['cardNumber'] = $get->cardNumber;
            $result['success'] = true;

        } while (false);

        return response()->json($result);
    }

    /**
     * @OA\Get(
     ** path="/api/agreementNew",
     *   tags={"Подпись по СМС"},
     *   summary="Подписание документов через СМС",
     *
     *  @OA\Parameter(
     *      name="leadID",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sign",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="true/false",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/

    public function agreementNew(Request $request)
    {
        $leadID = $request->input('leadID');
        $sign = $request->input('sign');
        $result['success'] = true;
        do {
            if (!$leadID) {
                $result['message'] = 'Не передан токен';
                $result['success'] = false;
                break;
            }


            $res = DB::table('new_short_url')->select('*')
                ->where('leadID', $leadID)->first();

            if (!$res) {
                $result['message'] = 'Не найден подходящих документов';
                $result['success'] = false;
                break;
            }
            DB::table('new_short_url')->where('leadID', $leadID)->update(['status' => 2]);

            $http = new Client(['verify' => false]);
            $responseUrl = "https://icredit-crm.kz/api/docs/signNew.php";
            try {
                $response = $http->get($responseUrl, [

                    'query' => [
                        'leadID' => $leadID,
                        'sign' => $sign,
                    ],
                ]);

                $result['message'] = 'Ваше согласие на обработке';
            } catch (BadResponseException $e) {
                $result['message'] = 'Попробуйте позже';
            }

        } while (false);

        return response()->json($result);
    }

    public function prolongation(Request $request)
    {
        $doc1 = $request->input('doc1');
        $doc2 = $request->input('doc2');
        $doc3 = $request->input('doc3');
        $doc4 = $request->input('doc4');
        $doc5 = $request->input('doc5');
        $dealID = $request->input('dealID');
        $token = Str::random(16);
        $result['success'] = false;
        do {
            if (!$dealID) {
                $result['message'] = 'Не передан номер сделки';
                break;
            }
            DB::table('prolongation')->insertGetId([
                'doc1' => $doc1,
                'doc2' => $doc2,
                'doc3' => $doc3,
                'doc4' => $doc4,
                'doc5' => $doc5,
                'dealID' => $dealID,
                'token' => $token,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $result['success'] = true;
            $result['url'] = "https://i-credit.kz/aggrements?token=$token";
        } while (false);
        return response()->json($result);

    }

    /**
     * @OA\Get(
     ** path="/api/getData",
     *   tags={"Подпись по СМС"},
     *   summary="Получение данные для подписание ресструктризацию",
     *
     *  @OA\Parameter(
     *      name="token",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="true/false",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/

    public function getData(Request $request)
    {
        $token = $request->input('token');
        $result['success'] = false;
        do {
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }
            $docs = DB::table('prolongation')->where('token', $token)->where('status', 1)->first();
            if (!$docs) {
                $result['message'] = 'Документы не найдены';
                break;
            }
            $result['id'] = $docs->dealID;
            $result['id_req'] = 1;
            $result['res'] = true;
            $result['success'] = true;
            $result['docs'] = [
                [
                    'name' => 'Заявление на страхование',
                    'link' => $docs->doc1,
                ],
                [
                    'name' => 'Согласие на страхование микрокредита',
                    'link' => $docs->doc2,
                ],
                [
                    'name' => 'Договор добровольного срочного страхования жизни',
                    'link' => $docs->doc3,
                ],
                [
                    'name' => 'Договор о предоставлении микрокредита',
                    'link' => $docs->doc4,
                ],
                [
                    'name' => 'Реструктуризация',
                    'link' => $docs->doc5,
                ]
            ];
        } while (false);
        return response()->json($result);
    }

    /**
     * @OA\Get(
     ** path="/api/agreement",
     *   tags={"Подпись по СМС"},
     *   summary="Подписание ресструктризацию по СМС",
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sign",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="true/false",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/

    public function agreement(Request $request)
    {
        $dealID = $request->input('id');
        $sign = $request->input('sign');
        $result['success'] = false;
        do {
            if (!$dealID) {
                $result['message'] = 'Не передан айди сделки';
                break;
            }
            if (!$sign) {
                $result['message'] = 'Не передан подпись';
                break;
            }
            $data = DB::table('prolongation_url')->where('id', $dealID)->first();
            if (!$data) {
                $result['message'] = 'Не найден документ';
                break;
            }
            $contractNumber = $data->contractNumber;
            DB::table('prolongation_url')->where('id', $dealID)->update(['status' => 2]);
            $url = "https://icredit-crm.kz/api/webhock/sign.php?sign=$sign&dealID=$contractNumber";
            $http = new Client(['verify' => false]);
            try {
                $http->get($url);
            } catch (BadResponseException $e) {
                info('bad sign ' . $dealID);
            }
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function agreementCabinet(Request $request){
        $dealID = $request->input('dealID');
        $result['success'] = false;
        do{
            if (!$dealID){
                $result['message'] = 'Не передан айди сделки';
                break;
            }
            $url = "https://icredit-crm.kz/api/webhock/prolongationSign.php?dealID=$dealID";
            $http = new Client(['verify' => false]);
            $http->get($url);
        }while(false);
        return response()->json($result);
    }

    public function prolongationData(Request $request){
        $result['success'] = true;
        $token = Str::random(8);
        $s = DB::table('prolongation_url')->insertGetId([
            'fio' => $request->input('fio'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'workPlace' => $request->input('workPlace'),
            'position' => $request->input('position'),
            'iin' => $request->input('iin'),
            'docNumber' => $request->input('docNumber'),
            'docIssue' => $request->input('docIssue'),
            'startGiven' => $request->input('startGiven'),
            'endGiven' => $request->input('endGiven'),
            'birthPlace' => $request->input('birthPlace'),
            'insurance' => $request->input('insurance'),
            'prolongationDate' => $request->input('prolongationDate'),
            'code' => $request->input('code'),
            'period' => $request->input('period'),
            'contractNumber' => $request->input('contractNumber'),
            'birthDay' => $request->input('birthDay'),
            'reward' => $request->input('reward'),
            'penalty' => $request->input('penalty'),
            'amount' => $request->input('amount'),
            'token' => $token,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $url = "https://i-credit.kz/aggrements?token=$token";
        if (!$s){
            $result['success'] = false;
            return response()->json($result);
        }
        $result['url'] = $url;
        return response()->json($result);
    }

    public function getProlongationData(Request $request){
        $token = $request->input('token');
        $result['success'] = false;
        do{
            $data = DB::table('prolongation_url')->where('token',$token)->where('status',1)->first();
            if (!$data){
                $result['message'] = 'Не найден документов';
                break;
            }
            $result['data'] = $data;
            $result['success'] = true;
        }while(false);
        return response()->json($result);
    }
}
