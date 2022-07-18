<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    /**
     * @OA\Get(
     ** path="/api/checkPerson",
     *   tags={"Восстановление пароля"},
     *   summary="Проверка пользователя",
     *
     *  @OA\Parameter(
     *      name="iin",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="phone",
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
    public function checkPerson(Request $request){
        $iin = $request->input('iin');
        $phone = $request->input('phone');
        $result['success'] = false;

        do {
            if (!$iin) {
                $result['message'] = 'Не передан иин';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            $user = User::where('iin', $iin)->where('phone', $phone)->first();
            if (!$user) {
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $id = $user->id;
            $url = "https://i-credit.kz/resetPassword?iin=$iin&phone=$phone&id=$id";

            DB::table('reset_password')->insertGetId([
                'iin' => $iin,
                'phone' => $phone,
                'short_url' => $url,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $sms = "http://37.18.30.37/api/resetPassword?phone=$phone&iin=$iin&id=$id";
            $smsResponse = file_get_contents($sms);
            $smsResponse = json_decode($smsResponse, true);

            if ($smsResponse['success'] == false) {
                $result['message'] = $smsResponse['message'];
                break;
            }

            $result['url'] = $url;
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }


    /**
     * @OA\Get(
     ** path="/api/checkUrl",
     *   tags={"Восстановление пароля"},
     *   summary="Проверка короткий ссылки",
     *
     *  @OA\Parameter(
     *      name="iin",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="phone",
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
    public function checkUrl(Request $request){
        $iin = $request->input('iin');
        $phone = $request->input('phone');
        $result['success'] = false;
        do {
            if (!$iin) {
                $result['message'] = 'Не передан иин';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            $check = DB::table('reset_password')
                ->where('iin', $iin)
                ->where('phone', $phone)
                ->where('status', 1)
                ->first();
            if (!$check) {
                $result['message'] = 'Не найдена ссылка';
                break;
            }
            $result['id'] = $check->id;
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }


    /**
     * @OA\Get(
     ** path="/api/resetPassword",
     *   tags={"Восстановление пароля"},
     *   summary="Изменение пароля",
     *
     *  @OA\Parameter(
     *      name="iin",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="id",
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
    public function resetPassword(Request $request)
    {
        $iin = $request->input('iin');
        $password = $request->input('password');
        $id = $request->input('id');
        $result['success'] = false;
        do {
            $user = User::where('iin', $iin)->first();
            if (!$user) {
                $result['message'] = 'Не найден пользователь';
                break;
            }
            User::where('iin',$iin)->update(['password' => bcrypt($password)]);
            DB::table('reset_password')
                ->where('id', $id)
                ->update(['status' => 2]);
            $result['success'] = true;
        } while (false);

        return response()->json($result);
    }
}
