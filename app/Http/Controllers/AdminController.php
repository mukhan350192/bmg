<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
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
            $admin = DB::table('super_user')->where('login', $login)->where('password',$password)->first();
            if (!$admin){
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
            $result['iin'] = $iin;
            $result['success'] = true;
            $result['fio'] = $user->name." ".$user->surname." ".$user->fatherName;
            $result['phone'] = $user->phone;
        }while(false);
        return response()->json($result);
    }

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
