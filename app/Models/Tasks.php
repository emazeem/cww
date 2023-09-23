<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;
    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function assets(){
        return $this->hasMany(TaskAsset::class,'task_id','id');
    }
    public function getTimeAttribute(){
        return date('g:i A',strtotime($this->attributes['time']));
    }
}
