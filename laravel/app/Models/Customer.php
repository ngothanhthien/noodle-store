<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    const REPUTATION_GOOD=1;
    protected $fillable = ['phone', 'address'];
}
