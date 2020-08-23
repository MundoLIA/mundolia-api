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

    protected $rules = [
        'username' => 'required|unique:users',
        'name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|max:255',
        'grade' => 'required|max:1',
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

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
        $this->attributes['password'] = str_replace("$2y$", "$2a$", $this->attributes['password']);
    }
}
