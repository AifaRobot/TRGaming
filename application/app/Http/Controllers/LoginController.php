<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {  
        return view('login');
    }

    public function doLogin()
    {
        // validate the info, create rules for the inputs
        $rules = array(
            'nickname'    => 'required', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
        );
        
        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);
        
        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
        
            $userdata = array(
                'nickname'     => Input::get('nickname'),
                'password'  => Input::get('password')
            );
        
            if (Auth::attempt($userdata)) {
        
                return Redirect::to('admin');
        
            } else {        
        
                return Redirect::to('login');
        
            }
        
        }
    }
}
