<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if ($order->state == Order::STATE_SUCCESS) {
            foreach ($order->meals as $meal) {
                $meal->buy_amount += $meal->pivot->quality;
                $meal->save();
            }
        }
    }
}
