<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage(){
        $ourName = 'Bruno';
        $animals = ['cat', 'dog', 'fish'];

        return view('homepage', ['name' => $ourName, 'catname' => 'Maria', 'allAnimals' => $animals]);
    }

    public function aboutPage(){
        return view('single-post');
    }
}
