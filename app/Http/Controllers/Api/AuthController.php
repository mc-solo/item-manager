<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Log in with a username or email plus password and return a
     * Sanctum token for the Flutter app.
     *
     * Same logic as the web login (AuthenticatedSessionController):
     * the login field is a username or an email, picked by whether it
     * contains '@', and 5 attempts are allowed per login + IP.
     * Auth::attempt is not used here because API routes are stateless
     * (no session middleware), so the credential check is done
     * directly with Hash::check instead.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            return response()->json([
                'message' => 'Too many login attempts. Please try again in '.$seconds.' seconds.',
                'retry_after' => $seconds,
            ], 429);
        }

        $field = str_contains($request->input('login'), '@') ? 'email' : 'username';

        $user = User::where($field, $request->input('login'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey($request));

            return response()->json(['message' => 'These credentials do not match our records.'], 401);
        }

        RateLimiter::clear($this->throttleKey($request));

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $user->createToken('flutter-app')->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out.']);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('login')).'|'.$request->ip());
    }
}
