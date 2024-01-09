<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;

class UserInvitation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    protected $fillable = [
        'email',
        'signature',
        'role_id',
        'expires_at'
    ];

    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }
}
