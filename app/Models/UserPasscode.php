<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class UserPasscode extends Model
{
    protected $fillable = [
        'user_id',
        'passcode',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * The user this passcode belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verify a plain passcode against the stored hashed value.
     */
    public function verify(string $passcode): bool
    {
        return Hash::check($passcode, $this->passcode);
    }

    /**
     * Mark the passcode as used and update the last_used_at timestamp.
     */
    public function markUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
