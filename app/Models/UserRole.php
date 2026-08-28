<?php

namespace App\Models;

use Database\Factories\UserRoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['start_at', 'end_at', 'is_active', 'assign_by', 'update_by', 'disabled_at', 'user_ref', 'role_ref'])]
#[Table(key: 'ref', keyType: 'string')]

class UserRole extends Model
{
    /** @use HasFactory<UserRoleFactory> */
    use HasFactory, HasUuids;

    // protected $fillable = ['start_at', 'end_at', 'is_active', 'assign_by', 'update_by', 'disabled_at', 'user_id', 'role_id'];

    protected function casts(): array
    {
        return [
            'start_at' => 'date',
            'end_at' => 'date',
            'disabled_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_ref', 'ref');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_ref', 'ref');
    }

    public function assignBy()
    {
        return $this->belongsTo(User::class, 'assign_by', 'ref');
    }

    public function updateBy()
    {
        return $this->belongsTo(User::class, 'update_by', 'ref');
    }
}
