<?php

namespace App\Services;

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
}