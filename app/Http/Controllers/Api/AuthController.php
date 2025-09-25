<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Register
     */
    public function register(RegisterRequest $request)
    {
        try {
            $payload = $request->validated();

            // 🔐 enforce default role
            $payload['role'] = 'customer';

            $user = $this->users->create($payload);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data'    => new UserResource($user),
                'meta'    => ['token' => $token],
                'message' => 'User registered successfully',
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Register error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            $user = $this->users->findByEmail($credentials['email']);

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data'    => new UserResource($user),
                'meta'    => ['token' => $token],
                'message' => 'Logged in',
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout (revoke current token)
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if ($user && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out',
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Current user
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource($request->user()),
        ], Response::HTTP_OK);
    }
}
