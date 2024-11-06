<?php

namespace App\Models\Abstract;

use Illuminate\Support\Str;

abstract class BaseUuidModel extends BaseModel
{
    public $incrementing = false;

    protected $keyType = 'string';

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
