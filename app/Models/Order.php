<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function car(){
        return $this->belongsTo(Car::class);
    }
    public function tasks(){
        return $this->hasMany(Tasks::class);
    }

    public function subscription(){
        return $this->belongsTo(Package::class,'subscription_id','id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($order) {
            $order->tasks()->delete();
        });
    }
}
