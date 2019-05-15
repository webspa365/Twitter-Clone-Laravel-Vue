<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;


Route::get('/image', function () {
    $path = public_path();
    $url = url('/');
    error_log(url('/'));
    //require '../vendor/autoload.php';

    Image::configure(array('driver' => 'gd'));
    //$url = 'http://localhost:8000/storage/jojo_0.jpg';
    $url = public_path().'/storage/images/jojo_0.jpg';
    $img = Image::make($url);
    $img->resize(400, 400);
    $img->save($url);
    return view('welcome');
});

// usage inside a laravel route
Route::get('/img', function()
{
    //$img = Image::make('http://localhost:8000/public/js/jojo_.jpg')->resize(300, 200);

    //return $img->response('jpg');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
