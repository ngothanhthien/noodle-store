<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\StaffResource;
use App\Models\Rule;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(UserStoreRequest $request){
        $form=$request->validated();
        $username=$this->convertNameToUserName($form['name']);
        if(User::where('username',$username)->exists()) {
           $username=$username.'_'.substr($form['phone'],-3);
        }
        $form['username']=$username;
        $password=Str::random(6);
        $form['password']=Hash::make($password);
        DB::beginTransaction();
        try{
            $user=User::create($form);
            $rules=[];
            foreach($request->rules as $rule){
                array_push($rules,['user_id'=>$user->id,'name'=>$rule]);
            }
            if(count($rules)>0){
                Rule::insert($rules);
            }
            $user->rules;
            DB::commit();
            return response([
                'username'=>$username,
                'password'=>$password,
                'user_info'=>new StaffResource($user)
        ],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response([
                'errors'=>$e->getMessage()
            ],config('apistatus.badRequest'));
        }
    }
    public function destroy(User $user,Request $request){
        if($request->user()->id==$user->id){
            return response(['errors'=>'Bad Request'], config('apistatus.badRequest'));
        }
        $user->delete();
        return response(['message'=>'success'],config('apistatus.ok'));
    }
    public function getAll(){
        
        return StaffResource::collection(User::withOrdersSummary()->with('rules')->latest()->get());
    }
    public function changePhone(User $user,Request $request){
        if(!$request->has('phone')){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        $user->phone = $request->phone;
        $user->save();
        return response(['message'=>'success'],config('apistatus.ok'));
    }
    public function changeRule(User $user,Request $request){
        if(!$request->has('rules')){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        if(!is_array($request->rules)){
            return response(['errors'=>'Bad request'],config('apistatus.badRequest'));
        }
        DB::beginTransaction();
        try{
            $user->rules()->delete();
            $id=$user->id;
            $rules=[];
            foreach($request->rules as $rule){
                array_push($rules,['user_id'=>$id,'name'=>$rule]);
            }
            Rule::insert($rules);
            $user->tokens()->delete();
            DB::commit();
            return response(['message'=>'success'],config('apistatus.ok'));
        }catch(Exception $e){
            DB::rollBack();
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }        
    }
    private function convertNameToUserName($str) {
        $str=strtolower($str);
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/( )/", '', $str);
		return $str;
	}
}
