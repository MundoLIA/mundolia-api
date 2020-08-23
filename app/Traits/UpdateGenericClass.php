<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

trait UpdateGenericClass{

    public static function updateData($uuid)
    {
        return self::where('uuid','like','%'.$uuid.'%')->firstOrFail()
            ->update(request()->all());
    }

    public static function updateDataId($id)
    {
        return self::where('id','like','%'.$id.'%')->firstOrFail()
            ->update(request()->all());
    }
}
