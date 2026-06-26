<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function users()
    {
        return view('users', ['option' => 'all']);
    }

    public function addUser()
    {
        return view('addUser');
    }

    public function selectorasList()
    {
        return view('selectoras');
    }

    public function doLogout()
    {
        Auth::logout(); // log the user out of our application
        return Redirect::to('login'); // redirect the user to the login screen
    }
    public function listUser($option)
    {
        $options = ['usersfinished', 'usersnotfinished', 'usersnotplayed'];
        if (in_array($option, $options)) {
            return view('users', ['option' => $option]);
        } else {
            return 'Not Found';
        }
    }
}
