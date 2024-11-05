<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user){
        if(auth()->user()->id == $user->id){
            return back()->with('failure', 'Você não pode se seguir!');
        }

        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();

        if($existCheck > 0){
            return back()->with('failure', 'Você já segue esse usuário!');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;
        $newFollow->save();

        return back()->with('success', 'Você agora segue '.$user->username.'!');
    }

    public function removeFollow(User $user){
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();
        return back()->with('success', 'Você deixou de seguir '.$user->username.'!');
    }
}
