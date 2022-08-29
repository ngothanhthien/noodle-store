<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\Rule;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(UserStoreRequest $request){
        $form=$request->only(['username', 'password','name','phone']);
        try{
            User::create($form);
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            return response([
                'errors'=>$e->getMessage()
            ],config('apistatus.badRequest'));
        }
    }
    public function destroy(User $user){
        $user->delete();
        return response(['message'=>'success'],config('apistatus.ok'));
    }
    public function getAll(){
        return User::paginate(10);
    }
    public function changePhone(User $user,Request $request){
        if(!$request->has('phone')){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        $user->phone = $request->phone;
        $user->save();
        return response(['message'=>'success'],config('apistatus.ok'));
    }
    public function changeRule($id,Request $request){
        if(!$request->has('rules')){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        if(!is_array($request->rules)){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        try{
            $collection = Rule::where('user_id',$id)->get(['id']);
            Rule::destroy($collection->toArray());
            foreach($request->rules as $rule){
                Rule::create([
                    'user_id'=>$id,
                    'name'=>$rule
                ]);
            }
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }        
    }
}
