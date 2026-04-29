<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = ['role_name', 'display_name', 'description', 'permissions_json', 'is_active', 'sort_order'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getPermissionsAttribute()
    {
        return $this->permissions_json ? json_decode($this->permissions_json, true) : [];
    }
}
