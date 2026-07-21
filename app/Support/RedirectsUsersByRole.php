<?php

namespace App\Support;

use App\Models\User;

trait RedirectsUsersByRole
{
    protected function redirectRouteFor(User $user): string
    {
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('business')) {
                return 'business.dashboard';
            }

            if ($user->hasRole('driver')) {
                return 'driver.dashboard';
            }

            return 'mobile.customer.home';
        }

        return match ($user->role ?? 'customer') {
            'business' => 'mobile.business.dashboard',
            'driver' => 'mobile.driver.dashboard',
            default => 'mobile.customer.home',
        };
    }

    protected function assignRole(User $user, string $role): void
    {
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);

            return;
        }

        if (
            array_key_exists('role', $user->getAttributes()) ||
            in_array('role', $user->getFillable(), true)
        ) {
            $user->forceFill([
                'role' => $role,
            ])->save();
        }
    }
}