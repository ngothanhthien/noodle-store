<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
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
        DB::beginTransaction();
        try{
            if($request->has('phone')&&$request->has('address')){
                $customer=Customer::updateOrCreate(['phone'=>$request->phone],['address'=>$request->address]);
            }
            $order=Order::create([
                'customer_id' => $request->has('phone')&&$request->has('address')?$customer->id:null,
                'user_id' => $request->user()->id,
                'state' => Order::STATE_DELIVERY,
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
            $orders=Order::paginate(10);
            return response(['orders'=>$orders],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function get(Order $order){
        try{
            $order->meals;
            return response([
                'order'=>$order,
            ],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function update(OrderUpdateRequest $request,Order $order){
        DB::beginTransaction();
        try{
            $user=$request->user();

            Customer::where('customer_id',$request->customer_id)->update(['address'=>$request->address]);
            if(!$user->tokenCan('admin')){
                $order->user_id=$user->id;
            }
            $order->state=$request->state;
            $order->payment_gate=$request->payment_gate;
            $order->total_price=$this->totalPrice($request->meals);
            $order->save();
            
            $order->meals()->sync($request->meals);

            DB::commit();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function destroy(Order $order){
        try{
            $order->delete();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    private function totalPrice($meals){
        $total=0;
        foreach($meals as $meal){
            $total+=$meal['quality']*$meal['price'];
        }
        return $total;
    }
}
