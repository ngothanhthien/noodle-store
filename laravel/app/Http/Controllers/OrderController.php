<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Meal;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(OrderStoreRequest $request){
        $total_price=0;
        $mealsInput=$request->meals;
        foreach($mealsInput as $id=>$quality){
            $meal=Meal::find($id);
            if(!$meal){
                return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
            }
            $total_price += $meal->price*intval($quality);
            $mealsInput[$id]['price']=$meal->price;
        }
        $state=Order::STATE_DELIVERY;
        if($request->payment_gate==Order::PAYMENT_GATE_AT_SHOP
        ||$request->payment_gate==Order::PAYMENT_GATE_TAKE_AWAY){
            $state=Order::STATE_SUCCESS;
        }
        DB::beginTransaction();
        try{
            if($request->has('phone')&&$request->has('address')){
                $customer=Customer::updateOrCreate(['phone'=>$request->phone],['address'=>$request->address]);
            }
            $order=Order::create([
                'customer_id' => $request->has('phone')&&$request->has('address')?$customer->id:null,
                'user_id' => $request->user()->id,
                'state' => $state,
                'payment_gate' =>$request->payment_gate,
                'total_price' => $total_price,
            ]);
            $order->meals()->attach($mealsInput);
            DB::commit();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getAll(){
        try{
            $orders=Order::with('customer')
            ->latest()
            ->paginate(Order::ORDER_PER_PAGE);
            return response(OrderResource::collection($orders)->response()->getData(true),config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function get($id){
        try{
           $order=Order::where('id',$id)->with('customer')->with('user')->with('meals')->first();
            return response(new OrderDetailResource($order),config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getByState($state){
        $orders=Order::with('customer')
        ->where('state',$state)
        ->latest()
        ->paginate(Order::ORDER_PER_PAGE);
        return response(OrderResource::collection($orders)->response()->getData(true),config('apistatus.ok'));
    }
    public function getByPhone($phone){
        $customer=Customer::where('phone',$phone)->first();
        return $customer;
    }
    public function update(OrderUpdateRequest $request,Order $order){
        DB::beginTransaction();
        try{
            $order->state=$request->state;
            $order->payment_gate=$request->payment_gate;
            $order->total_price=$this->totalPrice($request->meals);
            $order->meals()->sync($request->meals);
            if($order->isDirty('state')){
                if($order->state==Order::STATE_SUCCESS){
                    foreach($order->meals as $meal){
                        $meal->buy_amount+=$meal->pivot->quality;
                        $meal->save();
                    }
                }
                if($order->getOriginal('state')==Order::STATE_SUCCESS){
                    foreach($order->meals as $meal){
                        $meal->buy_amount-=$meal->pivot->quality;
                        $meal->save();
                    }
                }
            }
            $order->save();
            DB::commit();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function fail(Order $order,Request $request){
        if($this->validatedUser($request->user(),$order)){
            return response(['errors'=>'Unauthorized'],config('apistatus.unauthorized'));
        }
        if(!$order->state==Order::STATE_SUCCESS&&!$order->state==Order::STATE_DELIVERY){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        $order->state=Order::STATE_FAIL;
        $order->save();
        return response($order,config('apistatus.ok'));
    }
    public function success(Order $order,Request $request){
        if($this->validatedUser($request->user(),$order)){
            return response(['errors'=>'Unauthorized'],config('apistatus.unauthorized'));
        }
        if(!$order->state==Order::STATE_DELIVERY){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        $order->state=Order::STATE_SUCCESS;
        $order->save();
        return response($order,config('apistatus.ok'));
    }
    public function cancel(Order $order,Request $request){
        if($this->validatedUser($request->user(),$order)){
            return response(['errors'=>'Unauthorized'],config('apistatus.unauthorized'));
        }
        $order->state=Order::STATE_CANCEL;
        $order->save();
        return response($order,config('apistatus.ok'));
    }
    private function validatedUser($user,$order){
        if(!$user->tokenCan('admin')&&$user->id!=$order->user_id){
            return false;
        }
        return true;
    }
    private function totalPrice($meals){
        $total=0;
        foreach($meals as $meal){
            $total+=$meal['quality']*$meal['price'];
        }
        return $total;
    }
}
