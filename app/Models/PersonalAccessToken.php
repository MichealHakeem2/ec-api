<?php
// app/Models/PersonalAccessToken.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalAccessToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'tokenable_type',
        'tokenable_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'timestamp',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the tokenable entity that the token belongs to.
     */
    public function tokenable()
    {
        return $this->morphTo();
    }

    /**
     * Update the last used at timestamp.
     */
    public function updateLastUsedAt()
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Delete the token and update the last used at timestamp.
     */
    public function delete()
    {
        $this->updateLastUsedAt();
        parent::delete();
    }
    protected static function booted()
    {
        static::creating(function ($token) {
            $token->expires_at = now()->addMinutes(120)->timestamp;
        });
    }
}
