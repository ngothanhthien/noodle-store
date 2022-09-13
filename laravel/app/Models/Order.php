<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    const STATE_DELIVERY =1;
    const STATE_SUCCESS =0;
    const PAYMENT_GATE_TAKE_AWAY=1;
    const PAYMENT_GATE_AT_SHOP=0;
    const PAYMENT_GATE_CALL=2;
    protected $fillable=['customer_id','user_id','state','payment_gate','total_price'];
    public function meals(){
        return $this->belongsToMany(Meal::class,'order_details');
    }
}
