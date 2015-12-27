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
        $sResponseData = '';
        if(Request::has('email') && Request::has('password') && Auth::attempt(['email'=> Request::get('email'), 'password'=> Request::get('password')]))
            $bResponse = 200;
        else
        {
            $bResponse = 401;
            $sResponseData = '<div class="alert alert-danger"><strong>Login failed</strong> Enter the correct email and password or register.</div>';
        }



        return response($sResponseData, $bResponse);
    }
    public function logout()
    {
        return response("logged out", (Auth::logout() ? 401 : 200));
    }
    public function register()
    {
        $iResponseCode = -1;
        $sResponseData = '';

        if(Request::has('email') && Request::has('password'))
        {
            // validate credentials, create user, login them in, return 200
            $validator = Validator::make(
                Request::only(['email','password']),
                [
                    'password' => 'required|min:6',
                    'email' => 'required|email|unique:users'
                ]
            );

            if ($validator->fails())
            {
                $sResponseData = '<div class="alert alert-danger"><strong>Registration failed</strong> Make sure your email and password meet the following requirements:';

                $iResponseCode = 412;
                $sResponseData .= '<ul>';
                foreach($validator->messages()->all('<li>:message</li>') as $message)
                {
                    //$sResponseData .= "<li><strong>sField</strong> $sError</li>";
                    $sResponseData .= $message;
                }
                $sResponseData .= '</ul></div>';

            }else{
                // succesful; create user, log them in, return 200
                $oUser = new User;
                $oUser->email = Request::get('email');
                $oUser->password = \Hash::make(Request::get('password'));

                $oUser->save();


                // create their default category
                $oCategory = new Category;
                $oCategory->user_id = $oUser->id;
                $oCategory->name = "all";
                $oCategory->save();

                Auth::attempt(['email' => $oUser->email, 'password' => $oUser->password], true);

                $iResponseCode = 200;
            }
        }else{
            $iResponseCode = 412;
            $sResponseData = '<div class="alert alert-danger"><strong>Registration failed</strong> Enter an email and password to register</div>';
        }
        return response($sResponseData, $iResponseCode);
    }

}