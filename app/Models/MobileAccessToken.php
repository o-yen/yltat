<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MobileAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'token_hash',
        'abilities_json',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'abilities_json' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = ['token_hash'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function issue(User $user, string $name = 'mobile', array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): array
    {
        $plain = Str::random(64);

        $token = static::create([
            'user_id' => $user->id,
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'abilities_json' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return [$token, $plain];
    }
}
