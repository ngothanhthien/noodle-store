<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Meal;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(OrderStoreRequest $request,OrderService $orderService){
        if($request->payment_gate==Order::PAYMENT_GATE_CALL&&(!$request->has('phone')||!$request->has('address'))){
            return response(['errors'=>"Bad request"],config('apistatus.badRequest'));
        }
        $orderService->create($request->input(),$request->user());
        return response(['message'=>'success'], config('apistatus.ok'));
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
    public function fail(Order $order,Request $request,OrderService $orderService){
        if(!$this->validatedUser($request->user(),$order)){
            return response(['errors'=>'Unauthorized'],config('apistatus.forbidden'));
        }
        if($order->state!=Order::STATE_SUCCESS&&$order->state!=Order::STATE_DELIVERY){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        $orderService->setState(Order::STATE_FAIL,$order);
        return response($order,config('apistatus.ok'));
    }
    public function success(Order $order,Request $request,OrderService $orderService){
        if(!$this->validatedUser($request->user(),$order)){
            return response(['errors'=>'Unauthorized'],config('apistatus.forbidden'));
        }
        if($order->state!=Order::STATE_DELIVERY){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        $orderService->setState(Order::STATE_SUCCESS,$order);
        return response($order,config('apistatus.ok'));
    }
    public function cancel(Order $order,Request $request,OrderService $orderService){
        if(!$this->validatedUser($request->user(),$order)){
            return response(['errors'=>'Unauthorized'],config('apistatus.forbidden'));
        }
        $orderService->setState(Order::STATE_CANCEL,$order);
        return response($order,config('apistatus.ok'));
    }
    private function validatedUser($user,$order){
        if($user->tokenCan('admin')){
            return true;
        }
        if($user->id==$order->user_id){
            return true;
        }
        return false;
    }
}
