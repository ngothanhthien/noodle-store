<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use Exception;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function store(StoreMealRequest $request){
        $form=$request->validated();
        $form['image'] = $request->file('image')->store('public/meals');
        if($request->has('description')){
            $form['description']=$request->description;
        }
        try{
            $meal=Meal::create($form);
            return response(['meal'=>$meal],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));   
        }
    }
    public function update(UpdateMealRequest $request,Meal $meal){
        try{
            $meal->update($request->validated());
            return response(['meal'=>$meal],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getAll(){
        try{
            $meals=Meal::latest()->get();
            return response(['meals'=>MealResource::collection($meals)],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getNewMeal(){
        try{
            $meals=Meal::orderBy('created_at', 'DESC')->limit(Meal::MEAL_NEW_TOTAL)->get();
            return response(['meals'=>$meals],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getBestSellerMeal(){
        try{
            $meals=Meal::orderBy('buy_amount', 'DESC')->limit(Meal::MEAL_BEST_SELLER_TOTAL)->get();
            return response(['meals'=>$meals],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function destroy(Meal $meal){
        try{
            $meal->delete();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
}
