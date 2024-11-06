<?php

namespace App\Models\Abstract;

use Illuminate\Foundation\Auth\User;
use Watson\Validating\ValidatingTrait;

abstract class BaseUser extends User
{
    use ValidatingTrait;

    protected bool $throwValidationExceptions = true;

    protected $dateFormat = 'Y-m-d\TH:i:s.uP';

    abstract public function getRules(): array;
}
