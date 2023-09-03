<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    public function getCreatedAtAttribute()
    {
        return Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }
}
