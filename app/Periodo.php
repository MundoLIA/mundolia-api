<?php

namespace App;

use App\Traits\UpdateGenericClass;
use Illuminate\Database\Eloquent\Model;


class Periodo extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['id','periodo','name','is_active','is_current','description'];

}
