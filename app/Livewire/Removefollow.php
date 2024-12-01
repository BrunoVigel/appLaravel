<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Follow;
use Livewire\Component;

class Removefollow extends Component
{
    public $username;

    public function save(){
        if(!auth()->check()){
            abort(403, 'Unauthorized');
        }

        $user = User::where('username', $this->username)->first();

        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();
        session()->flash('success', 'Você deixou de seguir!');
        return $this->redirect("/profile/{$user->username}", navigate:true);
    }

    public function render()
    {
        return view('livewire.removefollow');
    }
}
