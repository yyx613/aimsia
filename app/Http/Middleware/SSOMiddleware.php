<?php

namespace App\Http\Middleware;

use App\Http\AimsiaApi;
use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Log;
use CoreComponentRepository;

class SSOMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user() == null) {
            $api = new AimsiaApi();
            $res = $api->sendSSORequest('GET', '/auth', []);
            if (isset($res->result) && $res->result == true && isset($res->user)) {
                $user = User::where('email', $res->user->email)->first();
                Auth::loginUsingId($user->id);

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    CoreComponentRepository::instantiateShopRepository();
                    return redirect()->route('admin.dashboard');
                } elseif (auth()->user()->user_type == 'seller') {
                    return redirect()->route('seller.dashboard');
                } else {
        
                    if (session('link') != null) {
                        return redirect(session('link'));
                    } else {
                        return redirect()->route('dashboard');
                    }
                }
            }
        }
        return $next($request);
    }
}
