<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Read-side copy of spatie's `roles` table on the vitahr connection.
 */
class Role extends Model
{
    protected $connection = 'vitahr';

    protected $fillable = ['name', 'guard_name'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id',
        );
    }
}
