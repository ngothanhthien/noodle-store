<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\StaffResource;
use App\Models\User;
use App\Services\StaffService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(UserStoreRequest $request,StaffService $staffService){
        return response($staffService->create($request->all()),config('apistatus.ok'));
    }
    public function destroy(User $user,Request $request){
        if(!$request->user()->tokenCan('admin')&&$request->user()->id==$user->id){
            return response(['errors'=>'Bad Request'], config('apistatus.badRequest'));
        }
        $user->delete();
        return response(['message'=>'success'],config('apistatus.ok'));
    }
    public function getAll(){
        return StaffResource::collection(User::withOrdersSummary()->with('rules')->latest()->get());
    }
    public function get(User $user){
        $user->rules;
        return new StaffResource($user);
    }
    public function update(UserUpdateRequest $request,User $user,StaffService $staffService){
        return response(new StaffResource($staffService->update($request->all(),$user)),config('apistatus.ok'));
    }

}
