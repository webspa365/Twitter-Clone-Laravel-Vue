<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $table = 'tweets';

    protected $fillable = [
        'userId', 'username', 'tweet'
    ];

    protected $attributes = [
        //'userId' => 0,
        //'username' => '',
        'replyTo' => '',
        //'tweet' => '',
        'images' => '',
        'video' => '',
        'dir' => '',
        'replies' => 0,
        'retweets' => 0,
        'likes' => 0,
        'hashtags' => '',
        'liked' => false,
        'retweeted' => false,
        'retweetedBy' => '',
        'time' => 0
    ];
}


/*
public function up()
{
    Schema::create('tweets', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('userId');
        $table->string('username');
        $table->string('replyTo');
        $table->text('tweet');
        $table->string('images');
        $table->string('video');
        $table->string('dir');
        $table->integer('replies');
        $table->integer('retweets');
        $table->integer('likes');
        $table->text('hashtags');
        $table->boolean('liked');
        $table->boolean('retweeted');
        $table->string('retweetedBy');
        $table->integer('time');
        $table->timestamps();
    });
}


*/
