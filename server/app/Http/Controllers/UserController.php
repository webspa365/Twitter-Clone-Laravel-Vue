<?php

namespace App\Http\Controllers;

use App\User;
use App\Relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Intervention\Image\ImageManagerStatic as Image;

class UserController extends Controller
{
    public function authenticate(Request $req)
    {
        $credentials = $req->only('username', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'msg' => 'Invalid credentials']);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'msg' => 'Could not create token']);
        }

        $user = User::where('username', $req->username)->first();
        return response()->json(['success' => true, 'user' => $user, 'token' => $token]);
    }

    public function register(Request $req)
    {
        error_log('UserController@register');
        $validator = Validator::make($req->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        error_log('0');

        if($validator->fails()){
          return response()->json([
            'success' => false,
            'msg' => $validator->errors()->toJson()
          ]);
        }

        error_log('1');

        $user = new User;
        $user->username = $req->username;
        $user->email = $req->email;
        $user->password = Hash::make($req->password);
        error_log('2');
        $user->save();

        error_log('3');

        $token = JWTAuth::fromUser($user);

        return response()->json(['success' => true, 'user' => $user, 'token' => $token], 201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }

    public function getUser(Request $req) {
        error_log('UserController@getUser');
        // get user
        $user = User::where('username', $req->username)->first();
        error_log('$req='+$req->username);
        if(!$user) return response()->json(['success' => false, msg => 'Not found']);

        // get auth user
        $token = $req->bearerToken();
        if(isset($token)) {
          JWTAuth::setToken($token);
          $auth = JWTAuth::authenticate();
          //if(!$auth) return response()->json(['success' => false, msg => 'Error: JWTAuth::authenticate()']);
        }

        // followed or note
        if(isset($auth)) {
          $count = Relationship::where('follower', $auth->id)->where('followed', $user->id)->count();
          if($count > 0) $user->followed = 1;
        }

        return response()->json(['success' => true, 'user' => $user]);
    }

    public function editUser(Request $req) {
        // get auth user
        $token = $req->bearerToken();
        if(!$token) return response()->json(['success' => false, msg => 'Error: $token is null.']);
        JWTAuth::setToken($token);
        $auth = JWTAuth::authenticate();
        if(!$auth) return response()->json(['success' => false, msg => 'Error: JWTAuth::authenticate()']);

        // get user to edit
        $user = User::where('username', $auth->username)->first();
        if(!$user) {
          return response()->json(['success' => false, msg => 'Error: User:where()']);
        }

        /*
        if($user.id != $auth.id) {
          return response()->json(['success' => false, msg => 'Error: $user.id != $auth.id']);
        }*/

        // upload bg and avatar
        $this->validate($req, [
          'username' => 'required|string|max:255',
          'email' => 'required|string|email|max:255',
          'name' => 'nullable|string|max:255',
          'bio' => 'nullable|string|max:1024',
  	    	'bg' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
          'avatar' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048', //only allow this type extension file.
    		]);

        if($req->get('name')) $user->name = $req->get('name');
        if($req->get('username')) $user->username = $req->get('username');
        if($req->get('email')) $user->email = $req->get('email');
        if($req->get('bio')) $user->bio = $req->get('bio');

        // upload bg
        $bg = $req->file('bg');
        if($bg) {
          $dir = 'storage/media/'.$auth->id.'/bg';
          $files = glob($dir.'/*');
          //error_log('$files='.json_encode($files));
          if($files) {
            foreach($files as $f) {
              unlink($f);
            }
          }
          $ext = $bg->extension();
    		  $path = $bg->move($dir, 'bg.'.$ext);
          $img = Image::make($path);
          if($img->height() < $img->width()/4) {
            $w = null;
            $h = 250;
          } else {
            $w = 1000;
            $h = null;
          }
          $img->resize($w, $h, function($constraint) {$constraint->aspectRatio();});
          $img->crop(1000, 250, null, 0);
          $img->backup();
          $img->save($dir.'/bg.'.$ext);
          $img->reset();
          $img->resize(300, 75);
          $img->save($dir.'/thumbnail.'.$ext);
          $user->bg = 'bg.'.$ext;
        }

        // upload avatar
        $avatar = $req->file('avatar');
        if($avatar) {
          $dir = 'storage/media/'.$auth->id.'/avatar';
          $files = glob($dir.'/*');
          //error_log('$files='.json_encode($files));
          if($files) {
            foreach($files as $f) {
              unlink($f);
            }
          }
          $ext = $avatar->extension();
      		$path = $avatar->move($dir, 'avatar.'.$ext);
          error_log($path);
          $img = Image::make($path);
          if($img->width() > $img->height()) {
            $w = null;
            $h = 150;
          } else {
            $w = 150;
            $h = null;
          }
          $img->resize($w, $h, function($constraint) {$constraint->aspectRatio();});
          $img->crop(150, 150);
          $img->backup();
          $img->save($dir.'/avatar.'.$ext);
          $img->reset();
          $img->resize(75, 75);
          $img->save($dir.'/thumbnail.'.$ext);
          $user->avatar = 'avatar.'.$ext;
        }

        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }
}
