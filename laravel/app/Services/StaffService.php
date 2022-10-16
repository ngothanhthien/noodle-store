<?php
namespace App\Services;

use App\Http\Resources\StaffResource;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffService{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService=$authService;
    }
    public function newPassword($user){
        $password=Str::random(6);
        $user->password=Hash::make($password);
        $user->save();
        $user->tokens()->delete();
        return $password;
    }
    public function create($form){
        DB::beginTransaction();
        $user=new User();
        $user->name=$form['name'];
        $user->phone=$form['phone'];
        $password=Str::random(6);
        $user->password=Hash::make($password);
        $user->username=$this->authService->generateUserName($form['name'],$form['phone']);
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
}