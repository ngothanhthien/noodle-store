<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if ($order->state == Order::STATE_SUCCESS) {
            $purchased=0;
            foreach ($order->meals as $meal) {
                $meal->buy_amount += $meal->pivot->quality;
                $purchased+= $meal->price*$meal->pivot->quality;
                $meal->save();
            }
            if(!is_null($order->customer_id)){
                $customer=Customer::find($order->customer_id);
                $customer->purchased+=$purchased;
                $customer->save();
            }
        }
    }
}
