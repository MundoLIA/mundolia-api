<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;


trait ModelUserTrait
{
    public function __construct(array $attributes = [])
    {
        $attributes['uuid'] = Uuid::uuid4();
        parent::__construct($attributes);
    }
}
