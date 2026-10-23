<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
=======
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
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
<<<<<<< HEAD
    protected $guard_name = 'web';
=======

>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
    protected $fillable = [
        'name',
        'email',
        'password',
<<<<<<< HEAD
        'employee_id'
=======
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
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

<<<<<<< HEAD
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
=======
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
    }
}
