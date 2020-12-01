<?php

namespace App\SyncModels;

use Illuminate\Database\Eloquent\Model;

class GroupUserEnrollment extends Model
{
    protected $table = 'group_user_enrollments';
    protected $fillable = [
        'user_id',
        'school_id',
        'group_id_community',
        'group_id_academy'
    ];
}
