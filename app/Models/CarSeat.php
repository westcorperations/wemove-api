<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarSeat extends Model
{
    use HasFactory;
    protected $table = "car_seats";
    protected $fillable = ['car_id','seat_name','status'];



    /**
     * Get the car that owns the CarSeat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Cars::class, 'car_id', 'id');
    }
}
