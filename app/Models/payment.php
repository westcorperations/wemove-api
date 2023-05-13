<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    use HasFactory;
    protected $table = "payments";
    protected $fillable = ['user_id','booking_id','refrence','amount','booking_no','status'];

}
