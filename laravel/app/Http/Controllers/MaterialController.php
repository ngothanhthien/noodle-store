<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialStoreRequest;
use App\Http\Requests\MaterialUpdateRequest;
use App\Models\Material;
use Exception;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function store(MaterialStoreRequest $request){
        try{
            Material::create($request->validated());
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function destroy(Material $material){
        try{
            $material->delete();
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response(['errors'=>$e->getMessage()],config('apistatus.badRequest'));
        }
    }
    public function getAll(){
        return Material::paginate(20);
    }
    public function update(Material $material,MaterialUpdateRequest $request){
        try{
            $material->update($request->all());
            return response('',config('apistatus.ok'));
        }catch(Exception $e){
            return response('',config('apistatus.badRequest'));
        }
    }
}
