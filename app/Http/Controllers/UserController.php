<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePhotoRequest;
use App\Http\Resources\UserResource;
use App\Traits\HasPhotoUpload;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HasPhotoUpload;

    public function myProfile(Request $request): UserResource
    {
        $user = $request->user();
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return UserResource::make($user);
    }

    /**
     * @param  \App\Http\Requests\UpdatePhotoRequest  $request
     * @return \App\Http\Resources\UserResource
     */
    public function changePhoto(UpdatePhotoRequest $request): UserResource
    {
        $this->uploadPhoto($request);

        return UserResource::make($request->user());
    }
}
