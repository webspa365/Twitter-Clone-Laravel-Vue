<?php

namespace App\Http\Controllers;

use App\Retweet;
use App\User;
use App\Tweet;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RetweetController extends Controller
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
        error_log('RetweetController@store $req->tweetId'.$req->tweetId);
        // get auth
        $token = $req->bearerToken();
        if(!$token) return response()->json(['success' => false, 'msg' => 'Error: $token is null.']);
        JWTAuth::setToken($token);
        $auth = JWTAuth::authenticate();
        if(!$auth) return response()->json(['success' => false, 'msg' => 'Error: JWTAuth::authenticate()']);
        error_log(1);
        $tweetId = $req->tweetId;
        if(!$tweetId) return response()->json(['success' => false, 'msg' => 'Error: $tweetId is null.']);
        error_log(2);
        $retweet = Retweet::where('userId', $auth->id)->where('tweetId', $tweetId)->first();
        if($retweet == null) {
          // Retweet
          $newRetweet = new Retweet(['userId' => $auth->id, 'tweetId' => $tweetId, 'username' => $auth->username]);
          $newRetweet->save();
          $retweeted = true;
        } else {
          // remove retweeted
          $retweet->delete();
          $retweeted = false;
        }
        error_log(3);
        $counts = update_counts($auth->id, $tweetId);
        error_log('$counts='.$counts['userC']);

        return response()->json([
          'success' => true, 'retweeted' => $retweeted, 'userC' => $counts['userC'], 'tweetC' => $counts['tweetC']
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
}

function update_counts($userId, $tweetId) {
  // count retweets by userId
  $userC = Retweet::where('userId', $userId)->count();
  $user = User::find($userId);
  $user->retweets = $userC;
  $user->save();

  // count retweets by tweetId
  $tweetC = Retweet::where('tweetId', $tweetId)->count();
  $tweet = Tweet::find($tweetId);
  $tweet->retweets = $tweetC;
  $tweet->save();

  $counts = array('userC' => $userC, 'tweetC' => $tweetC);
  error_log('update_counts() $userC='.$counts['userC'].' $tweetC='.$counts['tweetC']);

  return $counts;
}
