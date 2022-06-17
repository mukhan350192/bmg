<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoringController extends Controller
{
    public function scoringResult(Request $request)
    {
        $leadID = $request->input('leadID');
        $decision = $request->input('decision');
        $amount = $request->input('amount');
        $period = $request->input('period');
        $reward = $request->input('reward');
        $bmg = $request->input('bmg');
        $result['success'] = false;
        do {
            if (!$leadID) {
                $result['message'] = 'Не передан лид айди';
                break;
            }
            if ($bmg == 1) {
                $bmg = true;
            } else if ($bmg == 2) {
                $bmg = false;
            }
            if (isset($decision) && $decision == 0) {
                DB::table('decision')->insertGetId([
                    'leadID' => $leadID,
                    'decision' => false,
                    'bmg' => $bmg,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $result['success'] = true;
                break;
            }
            if (!$amount){
                $result['message'] = 'Не передан сумма';
                break;
            }
            if (!$period){
                $result['message'] = 'Не передан срок';
                break;
            }
            if (!$reward){
                $result['message'] = 'Не передан вознаграждение';
                break;
            }
            DB::table('decision_details')->insertGetId([
               'leadID' => $leadID,
                'amount' => $amount,
                'period' => $period,
                'reward' => $reward,
                'givenDate' => date('Y-m-d'),
                'endDate' => date('Y-m-d',Carbon::now()->addDays($period-1)),
                'lpDate' => date('Y-m-d',Carbon::now()->addDays(6)),
                'total' => $amount+$reward+$reward,
                'totalGrace' => $amount+$reward,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }
}
