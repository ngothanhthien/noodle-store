<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Models\Meal;
use Exception;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function store(StoreMealRequest $request){
        try{
            $meal=Meal::create([
                'name' => $request->name,
                'price' => $request->price
            ]);
            $meal->materials()->attach($request->materials);
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));   
        }
    }
    public function update(UpdateMealRequest $request,Meal $meal){
        try{
            $meal->update([
                'name' => $request->name,
                'price' => $request->price
            ]);
            $meal->materials()->sync($request->materials);
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getAll(){
        try{
            $meals=Meal::with('materials')->paginate(20);
            return response(['meals'=>$meals],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function destroy(Meal $meal){
        try{
            $meal->delete();
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
}
