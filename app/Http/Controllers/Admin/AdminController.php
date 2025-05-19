<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AdminController extends Controller
{
    public function index()
    {
        $admin = Admin::all();
        return response()->json([
            'status' => 'success',
            'data' => $admin,
        ]);
    }

    public function getUser()
    {
       $auth_user = Auth::user();
       $user = Admin::where('id', $auth_user->id)->first();
       return response()->json($user);
    }

    public function login(AdminLoginRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $admin,
            'status' => 'Login successful',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = Admin::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $token = Password::broker('admins')->createToken($user);
        $user->token = $token;
        $user->update();

        // $url = url('/admin/reset-password/' . $token . '?email=' . $request->email);
        // url localhost:3000
        $url = 'http://localhost:3000/admin/reset-password/' . $token . '?email=' . $request->email;

        $subject = 'Reset Password Notification';
        $body = 'Click here to reset your password: <a href="' . $url . '">Reset Password</a>';
        Mail::to($request->email)->send(new ForgotPasswordMail($subject, $body));
        return response()->json([
            'status' => 'success',
            'message' => 'Password reset link sent to your email address.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'token' => ['required', 'string'],
        ]);

        $user = Admin::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $response = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to reset password'], 500);
        }
    }
}
