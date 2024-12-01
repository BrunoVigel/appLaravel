<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Follow;
use Livewire\Component;

class Addfollow extends Component
{
    public $username;

    public function save(){
        if(!auth()->check()){
            abort(403, 'Unauthorized');
        }

        $user = User::where('username', $this->username)->first();

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

        session()->flash('success', 'Usuário seguido!');
        return $this->redirect("/profile/{$user->username}", navigate:true);
    }

    public function render()
    {
        return view('livewire.addfollow');
    }
}
