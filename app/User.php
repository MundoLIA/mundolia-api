<?php

namespace App;

use App\Traits\ModelUserTrait;
use App\Traits\UpdateGenericClass;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, ModelUserTrait, UpdateGenericClass;

    protected $guarded = [];

    protected $fillable = [

        'id', 'uuid', 'username', 'name','second_name', 'last_name', 'second_last_name', 'email', 'grade', 'avatar','password', 'last_login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime', 'member_since' =>'datetime', 'last_login' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
