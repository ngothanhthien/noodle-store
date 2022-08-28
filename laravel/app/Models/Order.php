<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    const STATE_DELIVERY =1;
    public function meals(){
        return $this->belongsToMany(Meal::class,'order_details');
    }
}
