<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Tweet;
use App\User;
use App\Retweet;
use App\Like;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ReplyController extends Controller
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
        error_log('ReplyController@store');
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

        // save as tweet
        $tweet = new Tweet;
        $tweet->userId = $auth->id;
        $tweet->username = $auth->username;
        $tweet->tweet = $req->tweet;
        $tweet->replyTo = json_encode(array('id' => $req->toId, 'username' => $req->toUsername));

        error_log('before save() $tweet =' . $tweet);
        $tweet->save();
        error_log('after save() $tweet->id =' . $tweet->id);

        // save ids in Reply
        $reply = new Reply;
        $reply->replyId = $tweet->id;
        $reply->replyTo = $req->toId;
        $reply->save();

        // update count tweets at user
        $countT = Tweet::where('userId', $auth->id)->count();
        $user = User::find($auth->id);
        $user->tweets = $countT;
        $user->save();

        // update count replies at parent tweet
        $countR = Reply::where('replyTo', $req->toId)->count();
        $replied = Tweet::find($req->toId);
        $replied->replies = $countR;
        $replied->save();

        return response()->json(['success' => true, 'reply' => $tweet, 'count' => $countT]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        error_log('ReplyController@show');
        // get replied
        $replied = Tweet::find($id);
        //error_log('$replied='.$replied);

        // get replies
        $ids = Reply::where('replyTo', $id)->orderBy('created_at', 'desc')->pluck('replyId');
        error_log('$ids='.$ids);
        if(isset($ids)) {
          $replies = Tweet::whereIn('id', $ids)->get();
        } else {
          $replies = array();
        }

        error_log('$replies='.$replies);

        // get avatars
        //$arr = array_merge(array($replied), $replies->toArray());
        $a = get_avatars(array($replied));
        $b = get_avatars($replies);
        $avatars = (isset($b)) ? ($a+$b) : $a;
        error_log('$avatars='.json_encode($avatars));

        return response()->json(['success' => true, 'replied' => $replied, 'replies' => $replies, 'avatars' => $avatars]);
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
  error_log('get_avatars()');
  $avatars = array();
  foreach($tweets as $t) {
    $u = User::find($t->userId);
    error_log('get_avatars() $u'.$u);
    if($u->avatar) $avatars[$u->username] = $u->avatar;
  }
  return $avatars;
}
