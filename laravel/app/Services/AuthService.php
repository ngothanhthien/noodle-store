<?php

namespace App\Services;

class AuthService{
    public function isAdmin($user){
        return $user->tokenCan('admin')?true:false;
    }
}