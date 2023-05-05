<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cars extends Model
{
    use HasFactory;
    protected $table = "cars";
    protected $fillable = ['category_id','seat_no','name','brand','model','price','status'];

    /**
     * Get the Category that owns the Cars
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'category_id');
    }
    /**
     * Get all of the seats for the Cars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seats(): HasMany
    {
        return $this->hasMany(CarSeat::class, 'car_id', 'id');
    }
}
