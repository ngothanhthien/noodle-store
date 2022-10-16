<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\StaffResource;
use App\Models\Admin;
use App\Models\User;
use App\Services\AuthService;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function userLogin(LoginRequest $request,AuthService $authService){
        $user=User::where('username',$request->username)->with('rules')->first();
        if(!$user){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        if(!Hash::check($request->password,$user->password)){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        return response(
            [
                'user'=>new StaffResource($user),
                'token'=>$authService->issueUserToken($user),
            ]
            ,config('apistatus.ok'));
    }
    public function changePassword(ChangePasswordRequest $request){
        $user=$request->user();
        if(!Hash::check($request->oldPassword,$user->password)){
            return response(['errors'=>'Wrong old password'],config('apistatus.loginFailed'));
        }
        $user->password = Hash::make($request->newPassword);
        $user->save();
        $user->tokens()->delete();
        return response(['message'=>'Success'],config('apistatus.ok'));
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response(['message'=>'success'],config('apistatus.ok'));
    }
    public function adminLogin(LoginRequest $request,AuthService $authService){
        $admin=Admin::where('username',$request->username)->first();
        if(!$admin){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        if(!Hash::check($request->password,$admin->password)){
            return response(['errors'=>'Wrong username or password'],config('apistatus.loginFailed'));
        }
        return response([
            'token' =>$authService->issueAdminToken($admin),
        ],config('apistatus.ok'));
    }
    public function getUserByToken(Request $request){
        $user=$request->user();
        if($user->tokenCan('admin')){
            return response(['user'=>$user,'role'=>'admin'],config('apistatus.ok'));
        }
        $user->rules;
        return response(['user'=>$user,'role'=>'user'],config('apistatus.ok'));
    }
    public function newPassword(User $user,StaffService $staffService){
       return response(['password'=>$staffService->newPassword($user),'username'=>$user->username],config('apistatus.ok'));
    }
}
