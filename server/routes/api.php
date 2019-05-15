<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $req) {
    return $req->user();
});

// routes to UserController
Route::post('register', 'UserController@register');
Route::post('login', 'UserController@authenticate');
Route::get('open', 'DataController@open');
Route::get('profile/{username}', 'UserController@getUser');
Route::post('profile/{username}', 'UserController@editUser');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
});


// routes to TweetController
Route::apiResource('tweets', 'TweetController');
Route::get('likedTweets/{id}', 'TweetController@showLikedTweets');

Route::apiResource('likes', 'LikeController');

Route::apiResource('retweets', 'RetweetController');

Route::apiResource('replies', 'ReplyController');

Route::apiResource('relationships', 'RelationshipController');
Route::get('relationships/following/{id}', 'RelationshipController@following');
Route::get('relationships/followers/{id}', 'RelationshipController@followers');
