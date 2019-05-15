<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'test';
    
    protected $attributes = [
        'userId' => 0,
        'username' => 'a',
        'tweet' => 'a',
    ];
}
