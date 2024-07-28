<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\Profile\ProfileResource;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{
    public function current()
    {
        return Response::success(
            message: __("Current user profile"),
            data: new ProfileResource(
                auth('api-user')
                    ->user()
            )
        );
    }


    public function update(UpdateProfileRequest $request)
    {
        auth('api-user')
            ->user()
            ->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
            ]);

        return Response::success(
            message: __("Profile updated successfully."),
            data: new ProfileResource(
                auth('api-user')
                    ->user()
            )
        );
    }


}
