<?php
namespace App\Services;

use App\Http\Resources\StaffResource;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffService{
    public function create($form){
        DB::beginTransaction();
        $user=new User();
        $user->name=$form['name'];
        $user->phone=$form['phone'];
        $password=Str::random(6);
        $user->password=Hash::make($password);
        $user->username=$this->generateUserName($form['name'],$form['phone']);
        $user->save();
        $this->createRule($form['rules'],$user->id);
        DB::commit();
        $user->rules;
        return [
            'password' => $password,
            'username' => $user->username,
            'user_info' =>new StaffResource($user),
        ];
    }
    public function update($form,$user){
        DB::beginTransaction();
        $user->phone=$form['phone'];
        $user->name=$form['name'];
        $user->save();
        Rule::where('user_id',$user->id)->delete();
        $this->createRule($form['rules'],$user->id);
        DB::commit();
        return $user;
    }
    private function createRule($rules,$user_id){
        $preparedRules=[];
        foreach($rules as $rule=>$subRules){
            foreach($subRules as $subRule){
                array_push($preparedRules,['name'=>$rule.':'.$subRule,'user_id'=>$user_id]);
            }
        }
        Rule::insert($preparedRules);
    }
    private function generateUserName($str,$phone) {
        $str=strtolower($str);
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/( )/", '', $str);
        if(User::where('username',$str)->exists()) {
            return $str.'_'.substr($phone,-3);
         }
		return $str;
	}
}