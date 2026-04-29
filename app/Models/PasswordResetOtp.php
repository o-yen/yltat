<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
        'used',
        'reset_token',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }

    public static function generateFor(int $userId): self
    {
        // Invalidate any previous OTPs for this user
        self::where('user_id', $userId)->update(['used' => true]);

        return self::create([
            'user_id'    => $userId,
            'otp'        => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => Carbon::now()->addMinutes(10),
            'used'       => false,
        ]);
    }
}
