<?php

namespace App\Http\Controllers;

use App\Relationship;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RelationshipController extends Controller
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
        error_log('RelationshipController@store $req->userId'.$req->userId);
        // get auth
        $token = $req->bearerToken();
        if(!$token) return response()->json(['success' => false, 'msg' => 'Error: $token is null.']);
        JWTAuth::setToken($token);
        $auth = JWTAuth::authenticate();
        if(!$auth) return response()->json(['success' => false, 'msg' => 'Error: JWTAuth::authenticate()']);

        $relationship = Relationship::where('follower', $auth->id)->where('followed', $req->userId)->first();
        if($relationship == null) {
          // follow
          $relationship = new Relationship;
          $relationship->follower = $auth->id;
          $relationship->followed = $req->userId;
          $relationship->save();
          $followed = true;
        } else {
          $relationship->delete();
          $followed = false;
        }

        $following = Relationship::where('follower', $auth->id)->count();
        $user = User::find($auth->id);
        $user->following = $following;
        $user->save();

        $followers = Relationship::where('followed', $req->userId)->count();
        $user = User::find($req->userId);
        $user->followers = $followers;
        $user->save();

        return response()->json([
          'success' => true, 'followed' => $followed, 'following' => $following, 'followers' => $followers
        ]);
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

    public function following($id) {
        error_log('RelationshipController@following');
        $rows = Relationship::where('follower', $id)->get();
        $users = array();
        foreach($rows as $r) {
          $u = User::find($r->followed);
          $u->followed = 1;
          array_push($users, $u);
        }
        return response()->json(['success' => true, 'users' => $users]);
    }

    public function followers($id, Request $req) {
        error_log('RelationshipController@followers');
        $token = $req->bearerToken();
        if(isset($token)) {
          error_log($token);
          JWTAuth::setToken($token);
          $auth = JWTAuth::authenticate();
        }
        $rows = Relationship::where('followed', $id)->get();
        error_log($rows);
        $users = array();
        foreach($rows as $r) {
          $u = User::find($r->follower);
          if(isset($auth)) {
            $count = Relationship::where('follower', $auth->id)->where('followed', $u->id)->count();
            if($count > 0) $u->followed = 1;
          }
          array_push($users, $u);
        }
        return response()->json(['success' => true, 'users' => $users]);
    }
}
