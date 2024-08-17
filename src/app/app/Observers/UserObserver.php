<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Address;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if (request()->has('addresses')) {
            foreach (request()->addresses as $addressData) {
                Address::create([
                    'user_id' => $user->id,
                    'address' => $addressData['address'],
                    'is_checkpoint' => $addressData['is_checkpoint'] ?? false,
                ]);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if (request()->has('addresses')) {
            Address::where('user_id', $user->id)->delete();
            foreach (request()->addresses as $addressData) {
                Address::create([
                    'user_id' => $user->id,
                    'address' => $addressData['address'],
                    'is_checkpoint' => $addressData['is_checkpoint'] ?? false,
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Address::where('user_id', $user->id)->delete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
