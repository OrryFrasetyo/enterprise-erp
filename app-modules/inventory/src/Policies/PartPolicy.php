<?php

namespace Modules\Inventory\Policies;

use App\Models\User;
use Modules\Inventory\Models\Part;

class PartPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Part $part): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Part $part): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Part $part): bool
    {
        return $user->hasRole('super_admin');
    }
}
