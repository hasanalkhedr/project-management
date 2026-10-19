<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-side copy of spatie's `permissions` table on the vitahr connection.
 */
class Permission extends Model
{
    protected $connection = 'vitahr';

    protected $fillable = ['name', 'guard_name'];
}
