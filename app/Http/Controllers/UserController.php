<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
    	$validated = $request->validate([
        	'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        	'password' => ['required', 'string', 'min:8', 'confirmed'],
    	]);

    	$user = User::create([
        	'email' => $validated['email'],
        	'password' => Hash::make($validated['password']),
    	]);

    	event(new Registered($user));

    // Создаём токен
    	$token = $user->createToken('auth_token')->plainTextToken;

    	return response()->json([
        	'message' => 'User registered successfully.',
        	'user' => $user,
        	'token' => $token,
    	], 201);
   }

    public function loginAuth(Request $request)
    {
    	$validated = $request->validate([
        	'email' => 'required|email',
        	'password' => 'required',
    	]);

    	$user = User::where('email', $validated['email'])->first();

    	if (!$user || !Hash::check($validated['password'], $user->password)) {
        	throw ValidationException::withMessages([
            	'email' => ['Неверные учетные данные'],
        	]);
    	}

    // Создаём токен
    	$token = $user->createToken('auth_token')->plainTextToken;

    	return response()->json([
        	'success' => true,
        	'user' => $user,
        	'token' => $token,
    	]);
    }

    public function logout(Request $request)
    {

    	$request->user()->currentAccessToken()->delete();

    	return response()->json([
        	'success' => true,
        	'message' => 'Logged out'
    	]);
    }


    public function forgotPasswordStore(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent to your email']);
        }

        return response()->json(['error' => 'Failed to send reset link'], 400);
    }

    public function resetPasswordUpdate(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'redirect' => '/',
                'message' => 'Password successfully changed'
            ]);
        }

        return response()->json(['error' => 'Failed to reset password'], 400);
    }

   public function changeEmail(Request $request)
   {
       $request->validate([
           'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

       $user = $request->user();
       $user->update(['email' => $request->input('email')]);

       return response()->json([
           'success' => true,
           'message' => 'Email обновлён.',
        ]);
   }
}
