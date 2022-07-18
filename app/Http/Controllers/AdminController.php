<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     ** path="/api/authAdmin",
     *   tags={"Админка"},
     *   summary="Авторизация админа",
     *
     *  @OA\Parameter(
     *      name="login",
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

    public function authAdmin(Request $request){
        $login = $request->input('login');
        $password = $request->input('password');
        $result['success'] = false;
        do{
            if (!$login){
                $result['message'] = 'Не передан логин';
                break;
            }
            if (!$password){
                $result['message'] = 'Не передан пароль';
                break;
            }
            $admin = DB::table('super_user')->where('login', $login)->first();
            if (!$admin){
                $result['message'] = 'Логин или пароль неправильный';
                break;
            }
            if (!Hash::check($password,$admin->password)){
                $result['message'] = 'Логин или пароль неправильный';
                break;
            }
            $token = sha1(Str::random(60));
            DB::table('super_user')->where('id',$admin->id)->update(['token'=>$token]);
            $result['success'] = true;
            $result['token'] = $token;
        }while(false);
        return response()->json($result);
    }

    /**
     * @OA\Post(
     ** path="/api/searchUser",
     *   tags={"Админка"},
     *   summary="Поиск пользователя",
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
     *      name="iin",
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

    public function searchUser(Request $request){
        $token = $request->input('token');
        $iin = $request->input('iin');
        $result['message'] = $token;
        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            if (!$iin){
                $result['message'] = 'Не передан иин';
                break;
            }
            $admin = DB::table('super_user')->where('token',$token)->first();
            if (!$admin){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $user = User::where('iin',$iin)->first();
            if (!$user){
                $result['message'] = 'Не найдено пользователей';
                break;
            }
            $result['id'] = $user->id;
            $result['iin'] = $iin;
            $result['success'] = true;
            $result['fio'] = $user->name." ".$user->surname." ".$user->fatherName;
            $result['phone'] = $user->phone;
        }while(false);
        return response()->json($result);
    }

    /**
     * @OA\Post(
     ** path="/api/deleteUser",
     *   tags={"Админка"},
     *   summary="Удаление пользователя",
     *
     *  @OA\Parameter(
     *      name="user_id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
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
    public function deleteUser(Request $request){
        $user_id = $request->input('user_id');
        $token = $request->input('token');
        $result['message'] = false;
        do{
            if (!$user_id){
                $result['message'] = 'Не передан юзер айди';
                break;
            }
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            User::where('id',$user_id)->delete();
            $result['success'] = true;
        }while(false);
        return response()->json($result);
    }
}
