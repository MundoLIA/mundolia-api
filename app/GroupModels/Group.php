<?php

namespace App\GroupModels;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    protected $fillable = [
        'id',
        'code',
        'name',
        'teacher_id',
        'school_id',
        'grade',
        'is_active',
        'created_at'
    ];

    protected $hidden = ['id',];

    public $timestamps = false ;
}
