<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerGetRequest;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(CustomerStoreRequest $request){
        try{
            Customer::create($request->validated());
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function update(CustomerUpdateRequest $request,Customer $customer){
        try{
            $customer->update($request->validated());
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function get(CustomerGetRequest $request){
        try{
            $customer=Customer::where('phone',$request->phone)->first();
            return response(['customer'=>$customer],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getAll(){
        return response(['customers'=>Customer::paginate(20)],config('apistatus.ok'));
    }
}
