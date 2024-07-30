<?php

namespace App\Http\Controllers\Api\V1\Authentication;

use App\Enums\IsUsed;
use App\Enums\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Authentication\LoginRequest;
use App\Http\Requests\Api\V1\Authentication\VerifyRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::query()->where('mobile', $request->input('mobile'))->first();
        if (empty($user)) {
            $user = User::query()
                ->create([
                    'mobile' => $request->input('mobile'),
                ]);
        }

        $latestValidCode = $user
            ->otps()
            ->where([
                'mobile' => $user->mobile,
            ])
            ->where('expires_at', '>', Carbon::now())
            ->whereNull('used_at')
            ->first();

        if ($latestValidCode && isset($latestValidCode->expires_at)) {
            return Response::success(
                message: __("A valid verification code is available."),
                data: [
                    'hashcode' => $latestValidCode->hashcode,
                    'expires_at' => $latestValidCode->expires_at,
                ]
            );
        }


        $newOtp = $user
            ->otps()
            ->create([
                'code' => mt_rand(111111, 999999),
                'hashcode' => Str::random(8),
                'mobile' => $user->mobile,
                'expires_at' => Carbon::now()->addMinutes(2),
                'is_used' => IsUsed::NO,
            ]);

//        TODO sms code

        return Response::success(
            message: __('Code generated successfully and sent via sms for entered mobile number.'),
            data: [
                'hashcode' => $newOtp->hashcode,
                'expires_at' => $newOtp->expires_at,
            ]
        );
    }


    public function verify(VerifyRequest $request)
    {
        $user = User::query()
            ->where('mobile', $request->input('mobile'))
            ->first();


        if (empty($user)) {
            return Response::error(
                code: SystemMessage::INVALID_OTP,
                message: __("Invalid credentials for a user.")
            );
        }

        $latestValidCode = $user
            ->otps()
            ->where([
                'mobile' => $user->mobile,
            ])
            ->where('hashcode', $request->input('hashcode'))
            ->where('expires_at', '>', Carbon::now())
            ->whereNull('used_at')
            ->first();


        if (empty($latestValidCode)) {
            return Response::error(
                code: SystemMessage::INVALID_OTP,
                message: __("Invalid credentials for a user.")
            );
        }

        $latestValidCode->update([
            'used_at' => Carbon::now(),
            'is_used' => IsUsed::YES
        ]);

        $token = $user->createToken('user_auth')->plainTextToken;


        return Response::success(
            message: __("Your authentication token has been successfully generated."),
            data: [
                'token' => $token,
                'has_name' => (bool)($user->first_name || $user->last_name),
            ]
        );
    }


    public function logout()
    {
        auth()
            ->user()
            ->currentAccessToken()
            ->delete();

        return Response::destroy();
    }
}
