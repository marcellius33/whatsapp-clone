<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\User;

class ContactService
{
    public function storeContact(array $input, User $user): void
    {
        $query = User::query();

        if (!empty($input['username'])) {
            $query->where('username', $input['username']);
        }

        if (!empty($input['phone_number'])) {
            $query->orWhere('phone_number', $input['phone_number']);
        }

        $contact_user = $query->first();

        $contact = new Contact([
            'user_id' => $user->id,
            'contact_user_id' => $contact_user->id,
        ]);
        $contact->save();
    }
}
