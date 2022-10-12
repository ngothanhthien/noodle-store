<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService{
    public function increasePurchase($amount,Customer $customer){
        $customer->purchased+=$amount;
        $customer->save();
    }
    public function decreasePurchase($amount,Customer $customer){
        $customer->purchased-=$amount;
        $customer->save();
    }
    public function updateOrCreateByPhone($phone,$address){
        return Customer::updateOrCreate(['phone'=>$phone],['address'=>$address]);
    }
}