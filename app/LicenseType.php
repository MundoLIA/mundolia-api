<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    protected $table = 'licenses_type';

    protected $fillable = ['description_license_type'];
}
