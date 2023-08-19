<?php

namespace App\Http\Controllers;

use App\Enums\TokenTypeEnum;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Models\User;
use App\Notifications\User\SendPasswordResetEmail;
use App\Services\TokenService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return responseJson(['error' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request, UserService $service): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password']);

        $user = $service->register($data);
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return responseJson(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return responseJson(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return responseJson([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function verifyEmail(VerifyEmailRequest $request, TokenService $tokenService, UserService $userService): JsonResponse
    {
        $userId = $tokenService->validateToken($request->input('token'), TokenTypeEnum::EMAIL_VERIFICATION);

        if($userId !== false) {
            $userService->verifyUserEmailNow($userId);
            return responseJson(null, 204);
        }
        else {
            return responseJson('Token is invalid', 400);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request, TokenService $service)
    {
        $user = User::whereEmail($request->input('email'))->first();

        if(!is_null($user)) {
            $token = $service->generateToken($user, TokenTypeEnum::PASSWORD_RESET);
            $user->notify(new SendPasswordResetEmail($token));
        }

        return responseJson('Success');
    }

    public function passwordReset(PasswordResetRequest $request, TokenService $tokenService)
    {
        $userId = $tokenService->validateToken($request->input('token'), TokenTypeEnum::PASSWORD_RESET);

        if($userId !== false) {
            $user = User::find($userId);
            $user->password = bcrypt($request->input('password'));
            $user->save();

            return responseJson('Success');
        }
        else {
            return responseJson('Token is invalid', 400);
        }
    }
}
