<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HasPhotoUpload;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HasPhotoUpload;

    /**
     * @param  \App\Http\Requests\AuthRegisterRequest  $request
     * @return \App\Http\Resources\UserResource
     */
    public function register(AuthRegisterRequest $request): UserResource
    {
        $user = User::create($request->validated());

        if ($request->hasFile('photo')) {
            $this->uploadPhoto($request);
        }

        return UserResource::make($user);
    }

    /**
     * @param  \App\Http\Requests\AuthLoginRequest  $request
     * @return \App\Http\Resources\UserResource
     */
    public function login(AuthLoginRequest $request): UserResource
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            abort(401, 'Invalid Credentials');
        }

        $user = User::where('email', $request->email)->first();
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return UserResource::make($user);
    }
}
