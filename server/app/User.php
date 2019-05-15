<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $attributes = [
        'name' => '',
        'bio' => '',
        'bg' => '',
        'avatar' => '',
        'tweets' => 0,
        'retweets' => 0,
        'followers' => 0,
        'following' => 0,
        'likes' => 0,
        'bookmarks' => 0,
        'followed' => false,
    ];
}


/*
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('username');
        $table->string('email');
        $table->string('password');
        $table->string('name');
        $table->string('bio');
        $table->string('avatar');
        $table->string('bg');
        $table->integer('tweets');
        $table->integer('retweets');
        $table->integer('followers');
        $table->integer('following');
        $table->integer('likes');
        $table->integer('bookmarks');
        $table->boolean('followed');
        $table->timestamps();
    });
}
*/
