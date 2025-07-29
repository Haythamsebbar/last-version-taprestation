<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Service $service)
    {
        return $user->id === $service->prestataire->user_id;
    }
}