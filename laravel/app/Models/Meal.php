<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    const MEAL_PER_PAGE=20;
    const MEAL_NEW_TOTAL=3;
    const MEAL_BEST_SELLER_TOTAL=3;
    public $fillable=['name','price','description'];
    public function materials(){
        return $this->belongsToMany(Material::class,'recipes');
    }
}
