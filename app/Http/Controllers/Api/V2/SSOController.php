<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Log;

class SSOController extends Controller
{
    public function logout(Request $request) {
        try {
            (new LoginController)->logout($request);

            return response()->json(['result' => true, 'message' => 'Logout successfully']);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['result' => true, 'message' => 'Failed to logout']);
        }
    }
}
