<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
}
