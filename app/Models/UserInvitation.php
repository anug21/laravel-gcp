<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class UserInvitation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'email',
        'role_id',
        'expires_at'
    ];

    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }
}
