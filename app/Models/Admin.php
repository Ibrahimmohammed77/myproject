<?php

namespace App\Models;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticate
{
    protected $table="admins";
    protected $primaryKey="email";

    protected $keyType="string";

    // protected $incrementing=false;

    protected $fillable=[
        'email','name','phone'
    ];

    
}
