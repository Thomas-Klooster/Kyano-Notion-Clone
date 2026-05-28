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
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    private function issueAuthTokens(User $user): array
    {
        return [
            'accessToken' => $user->createToken('access-token', ['*'], now()->addMinutes(90))->plainTextToken,
            'refreshToken' => $user->createToken('refresh-token', ['*'], now()->addDays(30))->plainTextToken,
        ];
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
        Auth::login($user);
        $request->session()->regenerate();
        $tokens = $this->issueAuthTokens($user);

        return response()->json([
            'message' => 'Geregistreerd!',
            'user'=> $user,
            'accessToken' => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken'],
        ], 201)

        ->cookie('refreshToken', $tokens['refreshToken'], 60 * 24 * 30, '/', null, true, true, false, 'lax')
        ->cookie('accessToken', $tokens['accessToken'], 90, '/', null, true, true, false, 'lax');
    }

    public function refresh(Request $request) {
            $refreshToken = $request->input('refreshToken');
        $token = $refreshToken ? PersonalAccessToken::findToken($refreshToken) : null;

        if (!$token || $token->name !== 'refresh-token' || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json(['message' => 'token verlopen'], 401);
        }

        $user = $token->tokenable;
        $token->delete();
        $tokens = $this->issueAuthTokens($user);

        return response()->json([
            'accessToken' => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken'],
        ])
        
        ->cookie('refreshToken', $tokens['refreshToken'], 60 * 24 * 30, '/', null, true, true, false, 'lax')
        ->cookie('accessToken', $tokens['accessToken'], 90, '/', null, true, true, false, 'lax');

    }

   public function login(LoginRequest $request)
{
    $credentials = $request->only('email', 'password');
    if (!Auth::attempt($credentials, $request->filled('remember'))) {
        return response()->json(['message' => 'Ongeldige inloggegevens.'], 401);
    }

        $user = Auth::user();
        $request->session()->regenerate();
        $tokens = $this->issueAuthTokens($user);

    
    return response()->json([
        'message' => 'Ingelogd!',
        'user' => Auth::user(),
        'accessToken' => $tokens['accessToken'],
        'refreshToken' => $tokens['refreshToken'],
        ])
        
        ->cookie('refreshToken', $tokens['refreshToken'], 60 * 24 * 30, '/', null, true, true, false, 'lax')
        ->cookie('accessToken', $tokens['accessToken'], 90, '/', null, true, true, false, 'lax');


}

public function logout(Request $request) {
    if ($request->user()) {
    $request->user()->tokens()->delete();
    }

    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'message' => 'Succesvol uitgelogd!'
    ])->withoutCookie(Cookie::create('XSRF-TOKEN'))
      ->withoutCookie(Cookie::create(config('session.cookie')));

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
        // Email
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
