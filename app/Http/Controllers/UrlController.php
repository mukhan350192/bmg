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
     *   tags={"?????????????? ???? ??????"},
     *   summary="?????????????????? ???????????? ?????? ????????????????????",
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
                $result['message'] = '???? ?????????????? ??????????!';
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
                $result['message'] = '???? ???????????? ???? ???????? ?????????????? ????????????????????.';
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
     *   tags={"?????????????? ???? ??????"},
     *   summary="???????????????????? ???????????????????? ?????????? ??????",
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
                $result['message'] = '???? ?????????????? ??????????';
                $result['success'] = false;
                break;
            }


            $res = DB::table('new_short_url')->select('*')
                ->where('leadID', $leadID)->first();

            if (!$res) {
                $result['message'] = '???? ???????????? ???????????????????? ????????????????????';
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

                $result['message'] = '???????? ???????????????? ???? ??????????????????';
            } catch (BadResponseException $e) {
                $result['message'] = '???????????????????? ??????????';
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
                $result['message'] = '???? ?????????????? ?????????? ????????????';
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
     *   tags={"?????????????? ???? ??????"},
     *   summary="?????????????????? ???????????? ?????? ???????????????????? ????????????????????????????????",
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
                $result['message'] = '???? ?????????????? ??????????';
                break;
            }
            $docs = DB::table('prolongation')->where('token', $token)->where('status', 1)->first();
            if (!$docs) {
                $result['message'] = '?????????????????? ???? ??????????????';
                break;
            }
            $result['id'] = $docs->dealID;
            $result['id_req'] = 1;
            $result['res'] = true;
            $result['success'] = true;
            $result['docs'] = [
                [
                    'name' => '?????????????????? ???? ??????????????????????',
                    'link' => $docs->doc1,
                ],
                [
                    'name' => '???????????????? ???? ?????????????????????? ????????????????????????',
                    'link' => $docs->doc2,
                ],
                [
                    'name' => '?????????????? ?????????????????????????? ???????????????? ?????????????????????? ??????????',
                    'link' => $docs->doc3,
                ],
                [
                    'name' => '?????????????? ?? ???????????????????????????? ????????????????????????',
                    'link' => $docs->doc4,
                ],
                [
                    'name' => '????????????????????????????????',
                    'link' => $docs->doc5,
                ]
            ];
        } while (false);
        return response()->json($result);
    }

    /**
     * @OA\Get(
     ** path="/api/agreement",
     *   tags={"?????????????? ???? ??????"},
     *   summary="???????????????????? ???????????????????????????????? ???? ??????",
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
                $result['message'] = '???? ?????????????? ???????? ????????????';
                break;
            }
            if (!$sign) {
                $result['message'] = '???? ?????????????? ??????????????';
                break;
            }
            $data = DB::table('prolongation')->where('dealID', $dealID)->first();
            if (!$data) {
                $result['message'] = '???? ???????????? ????????????????';
                break;
            }
            DB::table('prolongation')->where('dealID', $dealID)->update(['status' => 2]);
            $url = "https://icredit-crm.kz/api/webhock/sign.php?sign=$sign&dealID=$dealID";
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
}
