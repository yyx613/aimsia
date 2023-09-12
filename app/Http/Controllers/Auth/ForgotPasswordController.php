<?php

namespace App\Http\Controllers\Auth;

use App\Http\AimsiaApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Mail\SecondEmailVerifyMailManager;
use App\Utility\SmsUtility;
use Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $api = new AimsiaApi();
        $res = $api->sendRequest('POST', '/forgotpassword', $request->all());
        
        if (isset($res->result) && $res->result == false) {
            flash(translate('No account exists with this email'))->error();
            return back();
        } else if (isset($res->result) && $res->result == true) {
            return view('auth.passwords.reset');
        }
        
        flash(translate('Something went wrong'))->error();
        return back();
    }
}
