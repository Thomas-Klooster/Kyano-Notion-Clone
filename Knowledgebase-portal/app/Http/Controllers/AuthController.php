<?php

namespace App\Http\Controllers;
use App\Mail\ForgotMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LogoutRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpMail;

class AuthController extends Controller
{

    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'password'=> Hash::make($data['password']),
            'company' => $data['company'],
            'phone_number' => $data['phone_number']
            ]);
        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        
        return response()->json([
            'message' => 'Geregistreerd!',
            'user'=> $user,
        ], 201);
    }

   public function login(LoginRequest $request)
{
    $credentials = $request->only('email', 'password');
    if (!Auth::attempt($credentials, $request->filled('remember'))) {
        return response()->json(['message' => 'Ongeldige inloggegevens.'], 401);
    }
    
     if ($request->hasSession()) {
         $request->session()->regenerate();
     }
    
    return response()->json([
        'message' => 'Ingelogd!',
        'user' => Auth::user(),
        ]);

}

public function logout(LogoutRequest $request) {
    Auth::guard('web')->logout();

    if ($request->hasSession()) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return response()->json([
        'success' => true,
        'message' => 'Succesvol uitgelogd!'
        ]);

    }    



    /* ---------------------------------------------------------------------------------------------------
        ? PASSWORD RESET
    */

    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Huidige wachtwoord is onjuist.'], 422);
        }

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