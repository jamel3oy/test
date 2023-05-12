<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AuthController extends Controller
{
    /*
    * Redirect the user to the GitHub authentication page.
    *
    * @return \Illuminate\Http\Response
    */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
    * Obtain the user information from GitHub.
    *
    * @return \Illuminate\Http\Response
    */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();

        return dd($user);
        // $user->token;
    }

    /*
    * Redirect the user to the GitHub authentication page.
    *
    * @return \Illuminate\Http\Response
    */
    public function redirectToErp()
    {
        return Socialite::driver('laravelpassport')->redirect();
    }

    /**
    * Obtain the user information from GitHub.
    *
    * @return \Illuminate\Http\Response
    */
    public function handleErpCallback()
    {
        try {
            $user = Socialite::driver('laravelpassport')->user();

            return dd($user->user);
            // Handle the user's details

        } catch (InvalidStateException $e) {
            // return dd($e);
            return redirect()->route('home')->withErrors(['state' => 'An error occurred during the authentication process']);
        }
        // $user->token;
    }
}
