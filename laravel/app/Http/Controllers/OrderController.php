<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreAndUpdateCustomerRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderStoreWithNewCustomerRequest;
use App\Http\Requests\OrderStoreWithOldCustomerRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Customer;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function storeWithNewCustomer(OrderStoreWithNewCustomerRequest $request){
        DB::beginTransaction();
        try{
            $user=$request->user();

            $customer=Customer::create($request->customer);

            $order=new Order();
            $order->customer_id=$customer->id;
            if(!$user->tokenCan('admin')){
                $order->user_id=$user->id;
            }
            $order->state=Order::STATE_DELIVERY;
            $order->payment_gate=$request->payment_gate;
            $order->total_price=$this->totalPrice($request->meals);
            $order->save();
            
            $order->meals()->attach($request->meals);
            DB::commit();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function storeWithOldCustomer(OrderStoreWithOldCustomerRequest $request,$customer_id){
        DB::beginTransaction();
        try{
            $user=$request->user();
            $order=new Order();
            $order->customer_id=$customer_id;
            if(!$user->tokenCan('admin')){
                $order->user_id=$user->id;
            }
            $order->state=Order::STATE_DELIVERY;
            $order->payment_gate=$request->payment_gate;
            $order->total_price=$this->totalPrice($request->meals);
            $order->save();
            
            $order->meals()->attach($request->meals);
            DB::commit();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function storeAndUpdateCustomer(OrderStoreAndUpdateCustomerRequest $request, Customer $customer){
        DB::beginTransaction();
        try{
            $user=$request->user();

            $customer->address=$request->address;
            $customer->save();

            $order=new Order();
            $order->customer_id=$customer->id;
            if(!$user->tokenCan('admin')){
                $order->user_id=$user->id;
            }
            $order->state=Order::STATE_DELIVERY;
            $order->payment_gate=$request->payment_gate;
            $order->total_price=$this->totalPrice($request->meals);
            $order->save();
            
            $order->meals()->attach($request->meals);
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
