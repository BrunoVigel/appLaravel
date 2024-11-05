<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername' => ['required'],
            'loginpassword' => ['required']
        ]);

        if(auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])){
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Login efetuado com sucesso!');
        } else {
            return redirect('/')->with('failure', 'Usuário ou senha inválidos!');
        }
    }

    public function logout(){
        auth()->logout();
        return redirect('/')->with('success', 'Logout efetuado com sucesso!');
    }

    public function useCorrectHomepage(){
        if(auth()->check()){
            return view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(4)]);
        } else {
            return view('homepage');
        }
    }

    public function register(Request $request){
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:30', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Usuário criado com sucesso!');
    }

    private function getSharedData($user){
        $currentlyFollowing = 0;

        if(auth()->check()){
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        $posts = $user->posts()->latest()->get();
        $postsCount = $posts->count();

        $followerCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        View::share('sharedData', [ 'currentlyFollowing' => $currentlyFollowing, 
                                    'avatar' => $user->avatar, 
                                    'username' => $user->username, 
                                    'postsCount' => $postsCount,
                                    'followerCount' => $followerCount,
                                    'followingCount' => $followingCount]);
    }

    public function profile(User $user){
        $this->getSharedData($user);
        $posts = $user->posts()->latest()->get();
        $postsCount = $posts->count();
        return view('profile', ['posts' => $posts, 'postsCount' => $postsCount]);
    }

    public function profileFollowers(User $user){
        $this->getSharedData($user);

        return view('profile-followers', ['followers' => $user->followers()->latest()->get()]);
    }

    public function profileFollowing(User $user){
        $this->getSharedData($user);

        return view('profile-following', ['following' => $user->following()->latest()->get()]);
    }

    public function showAvatarForm(){
        return view('avatar-form');
    }

    public function storeAvatar(Request $request){
        $request->validate([
            'avatar' => ['required', 'image', 'max:5000']
        ]);

        $user = auth()->user();

        $filename = $user->id . '-' . uniqid() . '.jpg';
       
        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'));
        $imgData = $image->cover(120, 120)->toJpeg();
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if($oldAvatar != '/fallback-avatar.jpg'){
            Storage::delete(str_replace('/storage', 'public/', $oldAvatar));
        }

        return back()->with('success', 'Avatar atualizado com sucesso!');
    }
}
