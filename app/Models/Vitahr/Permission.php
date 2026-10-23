<?php

namespace App\Models\Vitahr;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * spatie's Permission model bound to the vitahr connection — permissions live
 * on the shared vitahr database.
 */
class Permission extends SpatiePermission
{
    protected $connection = 'vitahr';
}
