<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
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
                $bmg = 1;
            } else if ($bmg == 2) {
                $bmg = 0;
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
            DB::table('decision')->insertGetId([
                'leadID' => $leadID,
                'decision' => true,
                'bmg' => $bmg,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
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
            $s = DB::table('decision_details')->insertGetId([
               'leadID' => $leadID,
                'amount' => $amount,
                'period' => $period,
                'reward' => $reward,
                'givenDate' => date('Y-m-d'),
                'endDate' => date('Y-m-d',strtotime(Carbon::now()->addDays($period-1))),
                'lpDate' => date('Y-m-d',strtotime(Carbon::now()->addDays(6))),
                'total' => $amount+$reward+$reward,
                'totalGrace' => $amount+$reward,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            print_r($s);
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function getScore(Request $request){
        $token = $request->input('token');
        $result['success'] = false;
        do{
            if (!$token){
                $result['message'] = 'Не передан лид айди';
                break;
            }
            $user = User::where('token',$token)->first();
            if (!$user){
                $result['message'] = 'Пользователь не найден';
                break;
            }

            $decision = DB::table('decision')->where('leadID',$user->leadID)->first();
            if (!$decision){
                $result['message'] = 'Не найден решение';
                break;
            }
            $result['bmg'] = $decision->bmg;
            $result['success'] = true;
            if ($decision->decision == 0){
                $result['decision'] = false;
                break;
            }
            $decisionDetails = DB::table('decision_details')->where('leadID',$user->leadID)->first();
            $result['amount'] = $decisionDetails->amount;
            $result['leadID'] = $user->leadID;
            $result['period'] = $decisionDetails->period;
            $result['reward'] = $decisionDetails->reward;
            $result['givenDate'] = date('d.m.Y',strtotime($decisionDetails->givenDate));
            $result['endDate'] = date('d.m.Y',strtotime($decisionDetails->endDate));
            $result['lpDate'] = date('d.m.Y',strtotime($decisionDetails->lpDate));
            $result['total'] = $decisionDetails->total;
            $result['totalGrace'] = $decisionDetails->totalGrace;
            $result['decision'] = true;

        }while(false);
        return response()->json($result);
    }

    public function getDocumentData(Request $request){
        $token = $request->input('token');
        $result['success'] = false;
        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            $user = User::where('token',$token)->first();
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $leadID = $user->leadID;
            $http = new Client(['verify' => false]);
            $url = "https://ic24.almait.kz/api/site/result.php?leadID=$leadID";
            $response = $http->get($url);
            if ($response){
                return $response->getBody()->getContents();
            }
        }while(false);
        return response()->json($result);
    }
}
