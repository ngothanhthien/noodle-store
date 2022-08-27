<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $fillable=['name','price'];
    public function materials(){
        return $this->belongsToMany(Material::class,'recipes');
    }
}
