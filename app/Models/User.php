<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

/**
 * The panel's authenticated user — identical to the vitahr `users` table
 * record (management logs in with their existing VitaHR admin accounts).
 * All role/permission checks run against the vitahr schema through the
 * repointed spatie models.
 */
class User extends Vitahr\User implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('manage employees');
    }
}
