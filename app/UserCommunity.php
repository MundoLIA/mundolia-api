<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCommunity extends Model
{
    protected $connection = 'mysql2';
    protected $table = "phpfox_user";

    protected $fillable = ['user_id', 'user_group_id','email', 'full_name', 'password', 'gender', 'user_name', 'joined'];

    public $timestamps = false;


}
