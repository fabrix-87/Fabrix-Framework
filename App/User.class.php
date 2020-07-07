<?php

namespace App;

use System\Data\DataModel;

/**
 *
 */
class User extends DataModel
{
    protected $table = 'users';
    protected $primary = 'user_id';

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'gender',
        'birthday',
        'username',
        'registration_date',
        'last_visit',
        'level'
    ];

    protected $hidden = [
        'password'
    ];

    protected $validation = [
        'firstname' => 'required|min:3',
        'lastname' => 'required|min:3',
        'email' => 'required|email'
    ];

}



 ?>
