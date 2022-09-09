<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'phone',
    ];
    public function rules(){
        return $this->hasMany(Rule::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public static function withOrdersSummary(){
        return self::query()
        ->withCount(['orders as orders_today' => function ($q){
            $q
            ->where('create_at', '>=',  date('Y-m-d'))
            ->where('state',Order::STATE_SUCCESS);
        }])
        ->withCount(['orders as orders_this_month' => function ($q){
            $q
            ->where('create_at', '>=',  date('Y-m') . '-01')
            ->where('state',Order::STATE_SUCCESS);
        }]);
    }
}
