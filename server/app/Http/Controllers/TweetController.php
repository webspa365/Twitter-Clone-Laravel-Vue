<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\User;
use App\Like;
use App\Retweet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TweetController extends Controller
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
        error_log('TweetController@store $req->get("tweet")='.$req->tweet);
        $token = $req->bearerToken();
        JWTAuth::setToken($token);
        $auth = JWTAuth::authenticate();
        if(!isset($auth)) {
          return response()->json(['success' => false, 'msg' => 'Error: JWTAuth::authenticate()']);
        }

        error_log('before $req->validate()');
        $this->validate($req, [
          'userId' => 'nullable',
          'username' => 'nullable',
          'tweet' => 'required|max:512'
        ]);
        error_log('after $req->validate()');
        error_log('auth.id='.$auth->id.'/auth.username='.$auth->username.'/$req->tweet='.$req->tweet);

        $tweet = new Tweet;
        $tweet->userId = $auth->id;
        $tweet->username = $auth->username;
        $tweet->tweet = $req->tweet;
        error_log('after new Tweet $tweet =' . $tweet);
        $tweet->save();

        error_log('$tweet->save() =' . $tweet);

        $count = Tweet::where('userId', $auth->id)->count();
        // update User
        if($count) {
          $user = User::find($auth->id);
          $user->tweets = $count;
          $user->save();
        }

        return response()->json(['success' => true, 'tweet' => $tweet, 'count' => $count]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $req)
    {
        error_log('TweetController@show $id='.$id.' $req->skip='.$req->skip);
        // get tweets by userId
        $tweets = Tweet::where('userId', $id)->orderBy('created_at', 'desc')->skip($req->skip)->limit(5)->get();
        //error_log('after Tweet::where()');
        foreach($tweets as $t) {
          $t->time = strtotime($t->created_at);//strtotime($t->created_at);
        }

        $lastTime = $tweets[count($tweets)-1]->time;
        //error_log('$lastTime='.$lastTime);

        // get retweets by userId
        $rows = Retweet::where('userId', $id)->orderBy('created_at', 'desc')->skip($req->rSkip)->limit(5)->get();
        //error_log('$rows='.$rows);
        if(count($rows) > 0) {
          foreach($rows as $r) {
            error_log('$r='.$r);
            $t = Tweet::find($r->tweetId);
            $t->time = strtotime($r->created_at);//strtotime($r->created_at);
            $t->retweetedBy = ($r->username) ? $r->username : User::find($r->userId)->username;
            error_log('$t='.$t);
            if($t->time > $lastTime) {
              $tweets->push($t);
            }
            error_log('count($tweets)='.count($tweets));
          }
        }

        // liked or not and retweeted or not by auth user
        $token = $req->bearerToken();
        $tweets = by_auth($token, $tweets);

        // get avatars
        $avatars = get_avatars($tweets);

        // sort tweets
        $tweets = $tweets->toArray();
        $time = array();
        foreach ($tweets as $key => $row) {
            $time[$key] = $row['time'];
        }
        array_multisort($time, SORT_DESC, $tweets);

        return response()->json(['success' => true, 'tweets' => $tweets, 'avatars' => $avatars]);
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
    public function destroy($id, Request $req)
    {
        error_log('TweetController@destroy $id='.$id);
        $token = $req->bearerToken();
        JWTAuth::setToken($token);
        $auth = JWTAuth::authenticate();
        if(!isset($auth)) {
          return response()->json(['success' => false, 'msg' => 'Error: JWTAuth::authenticate()']);
        }

        $tweet = Tweet::find($id);
        if(!$tweet) return response()->json(['success' => false, 'msg' => 'Error: Not found a tweet.']);
        if($auth->id !== $tweet->userId) {
            return response()->json(['success' => false, 'msg' => 'Error: Not authorized for this tweet.']);
        }

        error_log('destroy $tweet='.$tweet);
        $tweet->delete();

        $count = Tweet::where('userId', $auth->id)->count();
        // update user
        if($count) {
          $user = User::find($auth->id);
          $user->tweets = $count;
          $user->save();
        }

        return response()->json(['success' => true, 'deletedId' => $id, 'count' => $count]);
    }

    public function showLikedTweets($id, Request $req)
    {
        error_log('TweetController@showLikedTweets $id='.$id);
        // get liked ids
        $likes = Like::where('userId', $id)->orderBy('created_at', 'DESC')->skip($req->skip)->limit(5)->get();
        //error_log('$likes='.$likes);
        $ids = array();
        foreach($likes as $l) {
          array_push($ids, $l->tweetId);
        }
        error_log('$ids='.json_encode($ids));

        // get tweets by userId
        $tweets = Tweet::whereIn('id', $ids)->get();
        error_log('after Tweet::where()');

        // liked or not and retweeted or not by auth user
        $token = $req->bearerToken();
        $tweets = by_auth($token, $tweets);

        // get avatars
        $avatars = get_avatars($tweets, 'cmpad');

        // sort by likes
        $arr = array();
        foreach($likes as $l) {
          foreach($tweets as $t) {
            if($l->tweetId == $t->id) array_push($arr, $t);
          }
        }
        error_log(json_encode($arr));

        return response()->json(['success' => true, 'tweets' => $arr, 'avatars' => $avatars]);
    }
}



function by_auth($token, $tweets) {
  if(isset($token)) {
    JWTAuth::setToken($token);
    $auth = JWTAuth::authenticate();
    // get liked or not and retweeted or not by auth user
    if(isset($auth)) {
      foreach($tweets as $t) {
        $likeC = Like::where('userId', $auth->id)->where('tweetId', $t->id)->count();
        if($likeC > 0) $t->liked = true;
        $retweetC = Retweet::where('userId', $auth->id)->where('tweetId', $t->id)->count();
        if($retweetC > 0) $t->retweeted = true;
      }
    }
  }
  return $tweets;
}

function get_avatars($tweets) {
  $avatars = array();
  foreach($tweets as $t) {
    $u = User::find($t->userId);
    if($u->avatar) $avatars[$u->username] = $u->avatar;
  }
  return $avatars;
}
