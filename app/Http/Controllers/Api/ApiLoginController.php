<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use Auth;
use Validator;

class ApiLoginController extends Controller
{
    

	/**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    

    public function register(Request $request)
    {
        
        	
		$validation = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'email' => 'required',
                'password' => 'required|min:4'
            ]
        );

    	if ($validation->fails()) {
            return response()->json(['error' => $validation->getMessageBag()->all(), 'message' => 'Account creation Failed'], 400);
        }

    	$user = User::create([
         'name'    	=> $request->name,
         'email'    => $request->email,
         'password' => $request->password,
         'dob' 		=> $request->dob,
         'city' 	=> $request->city,
         'country' 	=> $request->country,
     	]);

    	if($user) {
    		return response()->json(['data' => $user, 'message' => 'Account created Successfully'], 200);
    	}
    	return response()->json(['error' => 'Bad Request', 'message' => 'Account creation Failed'], 400);
        
        
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
       
    	$credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
        //->header('Access-Control-Allow-Origin', '*')
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() {
        return response()->json(JWTAuth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
