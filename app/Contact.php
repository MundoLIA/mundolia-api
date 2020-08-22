<?php

namespace App;

use App\Traits\UpdateGenericClass;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use UpdateGenericClass;

    protected $table = 'contacts';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $fillable = [
        'school_id', 'user_id', 'contact_type_id', 'phone_number'
    ];

    protected $casts = [
        'id' => 'string',
    ];
}
