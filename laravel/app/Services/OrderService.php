<?php

namespace App\Services;

use App\Models\Meal;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private $customerService;
    private $mealService;
    private $authService;
    public function __construct(CustomerService $customerService,MealService $mealService,AuthService $authService){
        $this->customerService = $customerService;
        $this->mealService = $mealService;
        $this->authService = $authService;
    }
    public function setState($newState, Order $order)
    {
        $oldState=$order->state;
        DB::beginTransaction();
        $order->state = $newState;
        $order->save();
        $this->updateBuyAmountOfMeal($oldState,$newState,$order->meals);
        DB::commit();
        if($this->isOrderHaveCustomer($order)){
            $this->updatePurchaseOfCustomer($oldState,$newState,$order);
        }
        return $order;
    }
    public function create($form,$user){
        DB::beginTransaction();
        $order=new Order();
        $meals=$this->prepareMealsInput($form['meals']);
        $order->total_price=$this->calculateTotalPrice($meals);
        $order->customer_id=null;
        if($this->isNewOrderHaveCustomer($form)){
            $customer=$this->customerService->updateOrCreateByPhone($form['phone'],$form['address']);
            $this->customerService->increasePurchase($order->total_price,$customer);
            $order->customer_id=$customer->id;
        }
        $order->state=$form['payment_gate']==Order::PAYMENT_GATE_CALL?$order->state=Order::STATE_DELIVERY:Order::STATE_SUCCESS;
        $order->user_id=$this->authService->isAdmin($user)?null:$user->id;
        $order->payment_gate=$form['payment_gate'];
        $order->save();
        $order->meals()->attach($meals);
        foreach($order->meals as $meal){
            $this->mealService->increaseBuyAmount($meal->pivot->quality,$meal);
        }
        DB::commit();
        return $order;
    }
    private function prepareMealsInput($meals){
        foreach($meals as $id=>$quality){
            $meal=Meal::find($id);
            $meals[$id]['price']=$meal->price;
        }
        return $meals;
    }
    private function calculateTotalPrice($meals){
        $total_price = 0;
        foreach($meals as $meal){
            $total_price += $meal['price']*intval($meal['quality']);
        }
        return $total_price;
    }
    private function updatePurchaseOfCustomer($oldState,$newState,$order){
        if($newState==Order::STATE_SUCCESS){
            $this->customerService->increasePurchase($order->total_price,$order->customer);
            return;
        }
        if($oldState==Order::STATE_SUCCESS){
            $this->customerService->decreasePurchase($order->total_price,$order->customer);
            return;
        }
    }
    private function updateBuyAmountOfMeal($oldState,$newState,$meals){
        if($newState==Order::STATE_SUCCESS){
            foreach($meals as $meal){
                $this->mealService->increaseBuyAmount($meal->pivot->quality,$meal);
            }
            return;
        }
        if($oldState==Order::STATE_SUCCESS){
            foreach($meals as $meal){
                $this->mealService->decreaseBuyAmount($meal->pivot->quality,$meal);
            }
            return;
        }
    }
    private function isNewOrderHaveCustomer($form){
        return array_key_exists('phone',$form)&&array_key_exists('address',$form)?true:false;
    }
    private function isOrderHaveCustomer($order){
        return $order->customer_id==null?false:true;
    }
}
