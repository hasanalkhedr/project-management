<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User account on the shared vitahr database. `HasRoles` resolves spatie's
 * Role/Permission models, which config/permission.php repoints to the Vitahr
 * subclasses — so role and permission checks all run against the vitahr
 * schema.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $connection = 'vitahr';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The vitahr backend wrote model_has_roles.model_type as 'App\Models\User',
     * so the morph class must stay that value rather than this namespaced class
     * (also keeps rows written by App\Models\User here interchangeable).
     */
    public function getMorphClass(): string
    {
        return 'App\\Models\\User';
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
