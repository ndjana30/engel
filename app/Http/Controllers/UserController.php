<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiResponseControlller
{

    public function __construct(){
        $this->middleware('auth:api', ['except'=> ['login','register', 'logout']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $this->middleware('admin');
            $userList = User::paginate(15, page: $request->page ?? 1);
            return $this->successResponse($userList);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($request->password);            
            // dd($data);
            $user = User::create($data);
            $accessToken = Auth::guard('api')->login($user);
            $response  = ['user' => $user, 'access_token' => $accessToken];
            return $this->returnSucces($response);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'=> 'email|required',
                'password'=> 'string|required|min:6',
            ]);
            $credentials = $request->only('email', 'password');
            $token = Auth::guard('api')-> attempt($credentials);
            if ($token) {
                $user = Auth::guard('api')->user();
                // $accessToken = $user->createToken('authToken')->accessToken;                
                $response = ['user' => $user, 'access_token' => $token];
                return $this->returnSucces($response);
            } else {
                return $this->returnError(new \Exception('Email ou mot de passe incorrect'), 'Email ou mot de passe incorrect', 401  );
            }
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }
    /**
     * Refresh the access token.
     */
    public function refreshToken(Request $request)
    {
        try {
            // $request->validate(['refresh_token'=> 'string|required']);
            // $refreshToken = $request->refresh_token;            
            $newAccessToken = Auth::guard('api')->refresh();
            // dd($newAccessToken);
            $response = ['access_token' => $newAccessToken];
            return $this->returnSucces($response);
        } catch (\Throwable $th) {
            // dd($th);
            return $this->returnError($th);
        }
    }
    /**
     * Display the specified resource.
     */
    public function me()
    {
        try {
            $user = Auth::user();
            return $this->returnSucces($user);
        } catch (\Throwable $th) {
            return $this->returnError($th);
        }
    }
   
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(UpdateUserRequest $request)
    {
        try {
            // dd($request->all());
            // return $this->returnSucces($request->all());
            $user = Auth::user();
           $user->fill($request->all());
           $user->save();
            return $this->returnSucces(Auth::user());
        } catch (\Throwable $th) {
            $this->returnError($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
