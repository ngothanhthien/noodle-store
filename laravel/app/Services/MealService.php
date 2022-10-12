<?php

namespace App\Services;

use App\Models\Meal;

class MealService{
    public function increaseBuyAmount($amount,Meal $meal){
        $meal->buy_amount+=$amount;
        $meal->save();
    }
    public function decreaseBuyAmount($amount,Meal $meal){
        $meal->buy_amount-=$amount;
        $meal->save();
    }
}
