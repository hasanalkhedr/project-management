<?php

namespace App\Models\Vitahr;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * spatie's Role model bound to the vitahr connection — roles live on the
 * shared vitahr database, so all of this app's permission lookups (including
 * Filament Shield) read and write them there.
 */
class Role extends SpatieRole
{
    protected $connection = 'vitahr';
}
