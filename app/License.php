<?php

namespace App;

use App\Traits\ModelLicenseTrait;
use App\Traits\UpdateGenericClass;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use Uuid, ModelLicenseTrait, UpdateGenericClass;

    protected $table = 'licenses';
    protected $guarded = [];

    protected $fillable = [
        'titular', 'email_admin','school_id', 'license_type_id', 'user_id', 'studens_limit',
    ];

    protected $casts = [
        'purchase_at' => 'datetime', 'expiration_date' => 'datetime'
    ];

}
