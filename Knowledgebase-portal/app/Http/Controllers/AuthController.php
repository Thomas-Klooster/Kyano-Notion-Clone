<?php

namespace App\Http\Controllers;
use App\Mail\ForgotMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    private const ACCESS_TOKEN_COOKIE = 'accessToken';
    private const REFRESH_TOKEN_COOKIE = 'refreshToken';
    private const ACCESS_TOKEN_MINUTES = 75;
    private const REFRESH_TOKEN_MINUTES = 60 * 24 * 30;

    private function issueAuthTokens(User $user) {
        return [
            'accessToken' => $user->createToken('access-token', ['access'], now()->addMinutes(self::ACCESS_TOKEN_MINUTES))->plainTextToken,
            'refreshToken' => $user->createToken('refresh-token', ['refresh'], now()->addDays(30))->plainTextToken,
        ];
    }

    private function tokenPayload(User $user, array $tokens, string $message) {
        return [
            'message' => $message,
            'user' => $user,
            'accessToken' => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken'],
            'tokenType' => 'Bearer',
            'expiresIn' => self::ACCESS_TOKEN_MINUTES * 60,
        ];
    }

    private function cookieSecure(): bool {
        return (bool) config('session.secure', app()->environment('production'));
    }

    private function accessTokenCookie(string $token) {
        return cookie(
            self::ACCESS_TOKEN_COOKIE,
            $token,
            self::ACCESS_TOKEN_MINUTES,
            '/',
            config('session.domain'),
            $this->cookieSecure(),
            true,
            false,
            config('session.same_site', 'lax')
        );
    }

    private function refreshTokenCookie(string $token) {
        return cookie(
            self::REFRESH_TOKEN_COOKIE,
            $token,
            self::REFRESH_TOKEN_MINUTES,
            '/',
            config('session.domain'),
            $this->cookieSecure(),
            true,
            false,
            config('session.same_site', 'lax')
        );
    }

    private function forgetAccessTokenCookie() {
        return Cookie::forget(self::ACCESS_TOKEN_COOKIE, '/', config('session.domain'));
    }

    private function forgetRefreshTokenCookie() {
        return Cookie::forget(self::REFRESH_TOKEN_COOKIE, '/', config('session.domain'));
    }

    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'password'=> Hash::make($data['password']),
            'company' => $data['company'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address']
            ]);
        $tokens = $this->issueAuthTokens($user);

        return response()->json($this->tokenPayload($user, $tokens, 'Geregistreerd'), 201)
        ->cookie($this->accessTokenCookie($tokens['accessToken']))
        ->cookie($this->refreshTokenCookie($tokens['refreshToken']));
    }

    public function refresh(Request $request) {
        $refreshToken = $request->input('refreshToken')
            ?: $request->bearerToken()
            ?: $request->cookie(self::REFRESH_TOKEN_COOKIE);
        $token = $refreshToken ? PersonalAccessToken::findToken($refreshToken) : null;

        if (!$token || !$token->can('refresh') || ($token->expires_at && $token->expires_at->isPast()))
        { 
        return response()->json([
        'message' => 'Token verlopen'
        ], 401)
        ->cookie($this->forgetAccessTokenCookie())
        ->cookie($this->forgetRefreshTokenCookie());
        }

        $user = $token->tokenable;
        $token->delete();
        $user->tokens()->where('name', 'access-token')->delete();
        $tokens = $this->issueAuthTokens($user);

        return response()->json($this->tokenPayload($user, $tokens, 'Tokens vernieuwd'))
        ->cookie($this->refreshTokenCookie($tokens['refreshToken']))
        ->cookie($this->accessTokenCookie($tokens['accessToken']));
    }

    public function login(LoginRequest $request) {

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Ongeldige inloggegevens.',
            ], 401);
        }

        $tokens = $this->issueAuthTokens($user);

        return response()->json($this->tokenPayload($user, $tokens, 'Tokens aangemaakt'))
        ->cookie($this->refreshTokenCookie($tokens['refreshToken']))
        ->cookie($this->accessTokenCookie($tokens['accessToken']));
    }

    public function logout(Request $request)
    {
        $refreshToken = $request->input('refreshToken')
            ?: $request->cookie(self::REFRESH_TOKEN_COOKIE);

        $personalAccessToken = $request->user()?->currentAccessToken();

        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        if ($personalAccessToken) {
            $personalAccessToken->delete();
        }

        if ($refreshToken && $token = PersonalAccessToken::findToken($refreshToken)) {
            $token->delete();
        }

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

    return response()->json([
        'success' => true,
        'message' => 'Succesvol uitgelogd!'
        ])
        ->cookie($this->forgetAccessTokenCookie())
        ->cookie($this->forgetRefreshTokenCookie())
        ->withoutCookie('XSRF-TOKEN');
    }


    /* ---------------------------------------------------------------------------------------------------
        ? PASSWORD RESET
    */

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        $user->update(['password' => Hash::make($request->password)]);
        Mail::to($user->email)->send(new ForgotMail());

        return response()->json(['message' => 'Wachtwoord is gereset.']);
    }

    /* --------------------------------------------------------------------------------------------------- 
        ? PASSWORD FORGET
    */
    public function forgotPassword(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $pool = '1234567890QWERTYUIOPASDFGHJKLZXCVBNM';
        $otp = substr(str_shuffle($pool), 0, 6);
        $user = User::where('email', $request->email)->first();

        DB::table('password_reset_tokens')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'token' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now(),
            ]
        );
        
        Mail::to($user->email)->send(new OtpMail($otp));
        return response()->json(['message' => 'Een verificatiecode is gestuurd naar jouw mail.']);
        }

        public function verifyOtp(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Not Found.'], 404);
            }

            $record = DB::table('password_reset_tokens')
                ->where('user_id', $user->id)
                ->where('expires_at', '>=', Carbon::now())
                ->first();

            if (!$record || !Hash::check($request->otp, $record->token)) {
                return response()->json(['message' => 'Onjuist of verlopen.'], 422);
            }

            $request->session()->put('password_reset_user_id', $user->id);
            $request->session()->put('password_reset_verified_at', now()->timestamp);

            return response()->json(['message' => 'Code geverifieerd.']);
        }
        
        public function newPassword(Request $request)
        {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);

            $userId = $request->session()->get('password_reset_user_id');
            $verifiedAt = $request->session()->get('password_reset_verified_at');

            if (!$userId || !$verifiedAt) {
                return response()->json(['message' => 'Reset session missing or expired.'], 403);
            }

            if (Carbon::createFromTimestamp($verifiedAt)->addMinutes(10)->isPast()) {
                $request->session()->forget([
                    'password_reset_user_id',
                    'password_reset_verified_at',
                ]);

                return response()->json(['message' => 'Reset session expired.'], 403);
            }

            $user = User::find($userId);

            if (!$user) {
                $request->session()->forget([
                    'password_reset_user_id',
                    'password_reset_verified_at',
                ]);

                return response()->json(['message' => 'User not found.'], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            $user->tokens()->delete();

            DB::table('password_reset_tokens')
                ->where('user_id', $user->id)
                ->delete();

            $request->session()->forget([
                'password_reset_user_id',
                'password_reset_verified_at',
            ]);

            $request->session()->regenerate();

            Mail::to($user->email)->send(new ForgotMail());

            return response()->json([
                'message' => 'Je wachtwoord is gereset.',
            ]);
        }


        public function resetPasswordSession(Request $request)
        {
            $userId = $request->session()->get('password_reset_user_id');
            $verifiedAt = $request->session()->get('password_reset_verified_at');

            if (!$userId || !$verifiedAt) {
                return response()->json(['message' => 'No active reset session.'], 403);
            }

            if (Carbon::createFromTimestamp($verifiedAt)->addMinutes(10)->isPast()) {
                $request->session()->forget([
                    'password_reset_user_id',
                    'password_reset_verified_at',
                ]);

                return response()->json(['message' => 'Reset session expired.'], 403);
            }

            return response()->json(['message' => 'Reset session active.']);
        }
}
