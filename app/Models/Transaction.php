<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $appends=['date'];
    public function getDateAttribute(){
        return date('Y-m-d',strtotime($this->created_at));
    }


}
