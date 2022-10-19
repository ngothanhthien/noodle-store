<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Http\Resources\ToppingResource;
use App\Models\Meal;
use App\Services\MealService;
use Exception;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function store(StoreMealRequest $request,MealService $mealService)
    {
        return response(new MealResource($mealService->create($request->all())), config('apistatus.ok'));
    }
    public function update(UpdateMealRequest $request, Meal $meal,MealService $mealService)
    {
       return response(new MealResource($mealService->update($meal,$request->all())),config('apistatus.ok'));
    }
    public function getAll()
    {
        try {
            $meals = Meal::latest()->get();
            return response(['meals' => MealResource::collection($meals)], config('apistatus.ok'));
        } catch (Exception $e) {
            return response(['errors' => $e->getMessage()], config('apistatus.badRequest'));
        }
    }
    public function getNewMeal()
    {
        try {
            $meals = Meal::orderBy('created_at', 'DESC')->limit(Meal::MEAL_NEW_TOTAL)->get();
            return response(['meals' => $meals], config('apistatus.ok'));
        } catch (Exception $e) {
            return response(['errors' => $e->getMessage()], config('apistatus.badRequest'));
        }
    }
    public function getBestSellerMeal()
    {
        try {
            $meals = Meal::orderBy('buy_amount', 'DESC')->limit(Meal::MEAL_BEST_SELLER_TOTAL)->get();
            return response(['meals' => $meals], config('apistatus.ok'));
        } catch (Exception $e) {
            return response(['errors' => $e->getMessage()], config('apistatus.badRequest'));
        }
    }
    public function destroy(Meal $meal)
    {
        try {
            $meal->delete();
            return response(['message' => 'success'], config('apistatus.ok'));
        } catch (Exception $e) {
            return response(['errors' => $e->getMessage()], config('apistatus.badRequest'));
        }
    }
    public function getToppings(Meal $meal, MealService $mealService)
    {
        if ($meal->type == Meal::TYPE_TOPPING) {
            return response(["errors" => 'Bad request'], config('apistatus.badRequest'));
        }
        return response(ToppingResource::collection($mealService->getToppings($meal)), config('apistatus.ok'));
    }
    public function getAllToppings()
    {
        return response(ToppingResource::collection(Meal::where('type', '=', Meal::TYPE_TOPPING)->latest()->get()), config('apistatus.ok'));
    }
}
