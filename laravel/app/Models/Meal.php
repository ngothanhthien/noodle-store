<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    const MEAL_NEW_TOTAL=3;
    const MEAL_BEST_SELLER_TOTAL=3;
    const TYPE_TOPPING=2;
    const TYPE_DRINK=1;
    const TYPE_MAIN=0;
    public $fillable=['name','price','description','image','type'];
}
