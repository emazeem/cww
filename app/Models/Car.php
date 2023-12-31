<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function order(){
        return $this->belongsTo(Order::class,'id','car_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($car) {
            $car->order()->delete();
        });
    }
}
