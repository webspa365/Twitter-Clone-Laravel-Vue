<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    protected $table = 'relationships';
}


/*
public function up()
{
    Schema::create('relationships', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('follower');
        $table->integer('followed');
        $table->timestamps();
    });
}
*/
