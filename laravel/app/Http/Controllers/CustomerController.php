<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerGetRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Exception;

class CustomerController extends Controller
{
    public function get(CustomerGetRequest $request)
    {
        try {
            $customer = Customer::where('phone', $request->phone)->first();
            return response(['customer' => new CustomerResource($customer)], config('apistatus.ok'));
        } catch (Exception $e) {
            return response(['errors' => $e->getMessage()], config('apistatus.badRequest'));
        }
    }
    public function getAll()
    {
        return response(CustomerResource::collection(Customer::orderBy('updated_at', 'desc')->paginate(20))->response()->getData(true), config('apistatus.ok'));
    }
    public function getById(Customer $customer){
        return response(new CustomerResource($customer), config('apistatus.ok'));
    }
}
