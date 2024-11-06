<?php

namespace App\Models\Passport;

use Illuminate\Support\Str;
use Laravel\Passport\Client;

class OauthClient extends Client
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->assignPrimaryKey();
    }

    protected function assignPrimaryKey(): void
    {
        $key = $this->getKeyName();
        if ($this->$key === null) {
            $this->$key = Str::ulid()->toRfc4122();
        }
    }
}
