<?php

namespace App\Services;

use App\Models\Meal;
use Illuminate\Support\Facades\DB;

class MealService{
    protected $imageService;
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function create($form){
        DB::beginTransaction();
        $meal=new Meal();
        $meal->type=$form['type'];
        $meal->price=$form['price'];
        $meal->name=$form['name'];
        if($form['description']){
            $meal->description=$form['description'];
        }
        $meal->image=$this->imageService->save($form['image'],'public/meals');
        $meal->save();
        $toppings=[];
        foreach($form['topping'] as $topping){
            array_push($toppings,['main_id' => $meal->id,'topping_id' => $topping]);
        }
        DB::table('topping')->insert($toppings);
        DB::commit();
        return $meal;
    }
    public function update(Meal $meal,$form){
        DB::beginTransaction();
        $meal->price=$form['price'];
        $meal->type=$form['type'];
        $meal->name=$form['name'];
        if(array_key_exists('description',$form)){
            $meal->description=$form['description'];
        }
        if(array_key_exists('image',$form)){
            $meal->image=$this->imageService->save($form['image'],'public/meals');
        }
        $toppings=[];
        DB::table('topping')->where('main_id','=',$meal->id)->delete();
        foreach($form['topping'] as $topping){
            array_push($toppings,['main_id' => $meal->id,'topping_id' => $topping]);
        }
        DB::table('topping')->insert($toppings);
        $meal->save();
        DB::commit();
        return $meal;
    }
    public function increaseBuyAmount($amount,Meal $meal){
        $meal->buy_amount+=$amount;
        $meal->save();
    }
    public function decreaseBuyAmount($amount,Meal $meal){
        $meal->buy_amount-=$amount;
        $meal->save();
    }
    public function getToppings(Meal $meal){
        return DB::table('topping')->where('main_id','=',$meal->id)
        ->join('meals','topping.topping_id','=','meals.id')
        ->select('name','meals.id')
        ->get();
    }
}
