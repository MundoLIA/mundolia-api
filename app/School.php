<?php

namespace App;

use App\Traits\UpdateGenericClass;
use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;


class School extends Model
{
    use UpdateGenericClass;

    protected $fillable = ['name'];
}
