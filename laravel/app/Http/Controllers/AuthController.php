<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function userLogin(LoginRequest $request){
        $user=User::where('username',$request->username)->first();
        if(!$user){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        if(!Hash::check($request->password,$user->password)){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        return response($this->userLoginProcessAndResponse($user),config('apistatus.ok'));
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response('',config('apistatus.ok'));
    }
    public function adminLogin(LoginRequest $request){
        $admin=Admin::where('username',$request->username)->first();
        if(!$admin){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        if(!Hash::check($request->password,$admin->password)){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        return response([
            'token' =>$this->issueAdminToken($admin),
        ],config('apistatus.ok'));
    }
    private function issueAdminToken($admin){
        $admin->tokens()->delete();
        return $admin->createToken('Admin Token',['admin'])->plainTextToken;
    }
    private function userLoginProcessAndResponse($user) {
        $user->tokens()->delete();
        $abilities =['user'];
        foreach($user->rules as $rule){
            array_push($abilities,$rule->name);
        }
        return [
            'user' => 
                [
                    'username' => $user->username,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'rules' => $abilities,
                ],
            'token'=>$user->createToken('User Token',$abilities)->plainTextToken,
        ];
    }
}
