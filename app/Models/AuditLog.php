<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'module_name',
        'action_type',
        'record_id',
        'old_value_json',
        'new_value_json',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $module, string $action, string|int|null $recordId = null, ?array $oldValue = null, ?array $newValue = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'module_name' => $module,
            'action_type' => $action,
            'record_id' => $recordId,
            'old_value_json' => $oldValue ? json_encode($oldValue) : null,
            'new_value_json' => $newValue ? json_encode($newValue) : null,
            'created_at' => now(),
        ]);
    }
}
