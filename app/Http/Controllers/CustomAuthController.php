<?php


namespace App\Http\Controllers;

use App\Http\Middleware\Authenticate;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Support\Facades\Request;


use Auth;

class CustomAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */


    public function login()
    {
        //print_r(Auth::attempt(['email'=> Request::get('email'), 'password'=> Request::get('password')]));exit();
        $bResponse = null;
        if(Auth::attempt(['email'=> Request::get('email'), 'password'=> Request::get('password')]))
            $bResponse = 200;
        else
            $bResponse = 401;
        return response("$bResponse", $bResponse);
    }
    public function logout()
    {
        return response("logged out", (Auth::logout() ? 401 : 200));
    }





}
