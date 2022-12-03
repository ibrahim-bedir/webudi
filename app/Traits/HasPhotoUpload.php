<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HasPhotoUpload
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function uploadPhoto(Request $request): void
    {
        $user = $request->user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->photo = $request->file('photo')->store('users', 'public');
        $user->save();
    }
}
