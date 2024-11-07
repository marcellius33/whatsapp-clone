<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ThrottlesAttempts;
use App\Http\Helpers\RequestHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @group [User] Auth
 */
class AuthController extends AccessTokenController
{
    use ThrottlesAttempts;

    protected function throttleKeyPrefix(): string
    {
        return 'user_login';
    }

    /**
     * Profile
     */
    public function profile(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Register
     *
     * @bodyParam username string required Example: paul33
     * @bodyParam phone_number string required Example: 088123123123
     * @bodyParam name string required Example: Paul
     * @bodyParam password string required Example: paul123
     * @bodyParam password_confirmation string required Example: paul123
     */
    public function register(ServerRequestInterface $request): JsonResponse
    {
        $body = $request->getParsedBody();

        $validator = validator($body, [
            'username' => 'required|string|unique:users,username',
            'phone_number' => 'required|string|unique:users,phone_number',
            'name' => 'required|string',
            'password' => ['required', Password::min(6)->letters()->numbers(), 'confirmed'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->messages());
        }

        $user = $this->checkUserExists($body);

        if ($user !== null && $user->password !== null) {
            throw ValidationException::withMessages([
                'email' => [__('error.register_fail_email_exists')],
            ]);
        }

        DB::beginTransaction();
        try {
            $user = new User($body);
            $user->password = Hash::make($body['password']);
            $user->save();

            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            throw new BadRequestHttpException(__('error.register_fail'));
        }

        return response()->json([
            'message' => __('success.register_success'),
        ]);
    }

    /**
     * Login
     *
     * @bodyParam username string required Example: paul33
     * @bodyParam password string required Example: myevent
     */
    public function login(Request $request_http): array
    {
        $this->validateAttempts($request_http);

        $request = RequestHelper::createServerRequest($request_http);
        $body = $request->getParsedBody();
        $body['username'] = $body['username'];
        $body['client_id'] = config('passport.clients.users.id');
        $body['client_secret'] = config('passport.clients.users.secret');
        $body['grant_type'] = 'password';
        $body['scope'] = '';

        try {
            $result = json_decode($this->issueToken($request->withParsedBody($body))->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $this->clearAttempts($request_http);
        } catch (BadRequestHttpException $exception) {
            throw $exception;
        } catch (OAuthServerException | Exception $exception) {
            $this->incrementAttempts($request_http);
            throw new BadRequestHttpException(__('error.incorrect_credentials'));
        }

        return [
            'data' => $result,
            'message' => __('success.login_success')
        ];
    }

    /**
     * Change Password
     *
     * @bodyParam old_password string required Example: myevent
     * @bodyParam password string required Example: myevent123
     * @bodyParam password_confirmation string required Example: myevent123
     */
    public function changePassword(Request $request): UserResource
    {
        $input = $request->validate([
            'old_password' => 'required|string|min:6',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = $request->user();

        if (!Hash::check($input['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => [__('error.incorrect_password')],
            ]);
        }

        $user->password = Hash::make($input['password']);
        $user->save();

        return (new UserResource($request->user()))
            ->additional([
                'message' => __('success.change_password_success'),
            ]);
    }

    /**
     * Update Profile
     *
     * @bodyParam username string required Example: test
     * @bodyParam name string required Example: jake
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();
        $input = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'name' => 'required|string',
        ]);

        $user->fill($input);
        $user->save();

        return response()->json([
            'message' => __('success.update_profile_success'),
        ]);
    }

    /**
     * Refresh Token
     *
     * @bodyParam refresh_token string required Example: xxxxxxx
     */
    public function refreshToken(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();
        $body['client_id'] = config('passport.clients.users.id');
        $body['client_secret'] = config('passport.clients.users.secret');
        $body['grant_type'] = 'refresh_token';
        $body['scope'] = '';

        $key = 'refresh_token:' . $body['refresh_token'];

        $result = null;

        if (Redis::get($key) !== null) {
            $result = json_decode(Redis::get('refresh_token:' . $body['refresh_token']), true, 512, JSON_THROW_ON_ERROR);
        } else {
            $lock = Cache::lock($key, 2);

            try {
                $lock->block(3, function () use ($key, $body, $request, &$result) {
                    if (Redis::get($key) !== null) {
                        $result = json_decode(Redis::get('refresh_token:' . $body['refresh_token']), true, 512, JSON_THROW_ON_ERROR);
                    } else {
                        try {
                            $result = json_decode($this->issueToken($request->withParsedBody($body))->getContent(), true, 512, JSON_THROW_ON_ERROR);
                            Redis::set($key, json_encode($result, JSON_THROW_ON_ERROR, 512), 'EX', 45);
                        } catch (OAuthServerException | \Exception $exception) {
                            $result = null;
                        }
                    }
                });
            } catch (LockTimeoutException $exception) {
                throw $exception;
            } finally {
                $lock?->release();
            }
        }

        if ($result === null) {
            throw new BadRequestHttpException(__('error.incorrect_refresh_token'));
        }

        return [
            'data' => $result,
            'message' => __('success.refresh_token_success')
        ];
    }

    /**
     * Logout
     */
    public function logout(): array
    {
        try {
            $tokenRepository = new TokenRepository();
            $refreshTokenRepository = new RefreshTokenRepository();
            $tokenId = auth()->user()->token()->id;
            $tokenRepository->revokeAccessToken($tokenId);
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
        } catch (Exception) {
            throw new BadRequestHttpException(__('error.logout_failed'));
        }

        return [
            'message' => __('success.logout_success')
        ];
    }

    private function checkUserExists(array $input): ?User
    {
        try {
            return User::where('phone_number', $input['phone_number'])->firstOrFail();
        } catch (Exception) {
            return null;
        }
    }
}
