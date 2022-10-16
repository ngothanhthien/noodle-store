<?php

namespace App\Services;

use App\Models\User;

class AuthService{
    public function isAdmin($user){
        return $user->tokenCan('admin')?true:false;
    }
    public function issueUserToken($user){
        if($user->tokens->first()){
            $user->tokens()->delete();
        }
        $abilities =[];
        foreach($user->rules as $rule){
            array_push($abilities,$rule->name);
        }
        return $user->createToken('User Token',['user',...$abilities])->plainTextToken;
    }
    public function issueAdminToken($admin){
        if($admin->tokens->first()){
            $admin->tokens()->delete();
        }
        return $admin->createToken('Admin Token',['admin'])->plainTextToken;
    }
    public function generateUserName($str,$phone) {
        $str=strtolower($str);
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/( )/", '', $str);
        if(User::where('username',$str)->exists()) {
            return $str.'_'.substr($phone,-3);
         }
		return $str;
	}
}