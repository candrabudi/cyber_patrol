<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $login = $request->input('login');
        $password = $request->input('password');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $login,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role == 'superadmin') {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect_url' => route('superadmin.dashboard')
                ]);
            } else if (Auth::user()->role == 'admin') {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect_url' => route('admin.dashboard')
                ]);
            } else if (Auth::user()->role == 'reviewer') {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect_url' => route('reviewer.dashboard')
                ]);
            } else if (Auth::user()->role == 'customer') {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect_url' => route('customer.dashboard')
                ]);
            }
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
