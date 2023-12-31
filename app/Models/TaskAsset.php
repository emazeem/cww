<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAsset extends Model
{
    use HasFactory;
    public function getImagesAttribute()
    {
        return url('storage/tasks/'.$this->image);
    }
}
