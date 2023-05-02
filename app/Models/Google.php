<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Google extends Model
{
    use HasFactory;
   protected $table ='google';
    protected $fillable = [
        'user_id',
        'google_id',
        'access_token',
        'profile_img',
    ];



    /**
     * Get the user that owns the Google
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
