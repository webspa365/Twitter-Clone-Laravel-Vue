<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'replies';

    protected $attributes = [
    ];
}


/*
public function up()
{
    Schema::create('replies', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('replyId');
        $table->integer('replyTo');
        $table->timestamps();
    });
}
*/
