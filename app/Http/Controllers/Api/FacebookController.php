<?php

namespace App\Http\Controllers\api\one;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Socials\FacebookService;
use Auth;

class FacebookController extends Controller
{
    private $facebook;

    public function __construct (FacebookService $facebook) {
        $this->facebook = $facebook;
    }

    public function login(Request $request)
    {
        $request->validate([
            'token' => 'required', // FB ACCESS TOKEN COMING FROM FRONT END
            'email' => 'required|email', // EMAIL USED TO LOG IN FB COMING FROM FRONT END
            'name' => 'required' // NAME COMING FROM FRONT END
        ]);

        $token = $request->input('token');
        $userData = $request->except('token');
        
        $jwtToken = $this->facebook->setToken($token)->setData($userData)->getUserToken(true);

        return response()->json([
            'token' => $jwtToken,
            'data' => auth()->user()
        ])->setStatusCode(201);
    }
}
