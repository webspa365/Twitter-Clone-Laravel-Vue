<?php

namespace App\Http\Controllers;

use App\Test;
use Illuminate\Http\Request;
//use JWTAuth;
//use Tymon\JWTAuth\Exceptions\JWTException;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $req
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
      /*
        $token = $req->bearerToken();
        JWTAuth::setToken($token);
        $auth = JWTAuth::authenticate();
        if(!$auth || !$auth.id) {
          return response()->json(['success' => false, 'msg' => 'Error: JWTAuth::authenticate()']);
        }*/
        error_log('before new Test tweet='.$req->tweet);
        $test = new Test;
        error_log('after new Test');
        $test->userId = 0;//$auth->id;
        $test->username = 'a';//$auth->username;
        $test->tweet = $req->tweet;
        error_log('$test='.$test);
        $test->save();
        error_log('saved '.$test);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $req
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
