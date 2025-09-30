<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function updating(User $user): void
    {
        // Si el formulario de Admin envía el checkbox, lo forzamos en el modelo
        if (request()->has('trusted_seller')) {
            $incoming = request()->boolean('trusted_seller'); // true/false según el checkbox
            $user->setAttribute('trusted_seller', $incoming);
        }

        // Si cambió el valor (ya sea por request o manual), auditamos
        if ($user->isDirty('trusted_seller')) {
            if ((bool)$user->trusted_seller) {
                $user->trusted_at = now();
                $user->trusted_by = Auth::id();
            } else {
                $user->trusted_at = null;
                $user->trusted_by = null;
            }
        }
    }
}
