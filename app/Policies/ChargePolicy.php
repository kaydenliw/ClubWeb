<?php

namespace App\Policies;

use App\Models\Charge;
use App\Models\User;

class ChargePolicy
{
    public function view(User $user, Charge $charge): bool
    {
        return $user->organization_id === $charge->organization_id;
    }

    public function update(User $user, Charge $charge): bool
    {
        return $user->organization_id === $charge->organization_id;
    }

    public function delete(User $user, Charge $charge): bool
    {
        return $user->organization_id === $charge->organization_id;
    }
}
