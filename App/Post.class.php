<?php

namespace App;

use System\Data\DataModel;

/**
 *
 */
class Post extends DataModel
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'body',
        'description',
        'author_id'
    ];

    protected $validation = [
        'title' => 'required|min:3',
        'body' => 'required',
        'description' => 'required',
        'author_id' => 'required|int',
    ];

}



 ?>
