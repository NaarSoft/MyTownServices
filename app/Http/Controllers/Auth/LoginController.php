<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/response/index';

    protected $loginPath = '/login';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * validate login user email and password.
     *
     * @param \Illuminate\Http\Request $request.
     *
     * @return error message if user is deleted or inactive.
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);

        if(Auth::validate(['email' => $request->email, 'password' => $request->password, 'active' => 0])){
            return redirect($this->loginPath)
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Your account is Inactive or not verified',
                ]);
        }

        $credentials  = array('email' => $request->email, 'password' => $request->password);
        if (Auth::attempt($credentials, $request->has('remember'))){
            return redirect()->intended($this->redirectPath());
        }

        return redirect($this->loginPath)
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Incorrect email address or password',
            ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

         if($request->session_expire == 1){
             \Session::flash('session_expired_msg', "You have been logged off as you have not been using our website for more than ". floor(\Config::get('app.admin_session_timeout') / 60) ." minutes.");
         }
        return redirect('/login');
    }
}
