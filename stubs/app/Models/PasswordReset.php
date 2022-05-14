<?php

namespace App\Models;

use BlueFeather\EloquentFileMaker\Database\Eloquent\FMModel;

class PasswordReset extends FMModel
{

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

}
