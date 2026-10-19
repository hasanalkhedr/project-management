<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User account on the shared vitahr database.
 *
 * Roles and permissions live in the vitahr schema too, so this model uses
 * explicit relations against App\Models\Vitahr\Role / Permission instead of
 * spatie/laravel-permission's traits (those resolve against this app's own
 * connection and configured models).
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
     * so the morph class must stay that value rather than this namespaced class.
     */
    public function getMorphClass(): string
    {
        return 'App\\Models\\User';
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id',
        );
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id',
        );
    }

    public function assignVitahrRole(string $name, string $guard = 'web'): Role
    {
        $role = Role::where('name', $name)
            ->where('guard_name', $guard)
            ->firstOrFail();

        $this->roles()->syncWithoutDetaching([$role->id]);

        return $role;
    }

    /**
     * Check a spatie-style permission by name against the vitahr schema,
     * either granted directly or through an assigned role.
     */
    public function hasVitahrPermission(string $name): bool
    {
        return $this->permissions()->where('name', $name)->exists()
            || $this->roles()
                ->whereHas(
                    'permissions',
                    fn ($query) => $query->where('name', $name),
                )
                ->exists();
    }
}
