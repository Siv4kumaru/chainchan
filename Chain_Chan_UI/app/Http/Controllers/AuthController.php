<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    public function admin_only(){
                // Simplest "auth" check: Is the user logged in?
        if (!Auth::check()) {
            // If not logged in, redirect to login page
            // return redirect()->route('login'); // Assuming you have a named route 'login'
            abort(401, 'Unauthorized. Please log in.'); // Or just abort
        }

        // Simplest "admin" role check (assuming 'role' property on User model)
        if (Auth::user()->role !== 'admin') {
            // If not an admin, deny access
            // return redirect('/chat')->with('error', 'Access Denied: Admin Only.');
            abort(403, 'Access Denied: Admin Only.');
        }

        // If logged in and is admin, show the page
        // Pass the authenticated user to the view if needed (e.g., for display)
        $python_api=config('custom.python_api');
        return view('auth.knowledge', [ 'python_api' => $python_api]);
    }
    

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect('register')
                        ->withErrors($validator)
                        ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Optionally log the user in directly
        // Auth::attempt($request->only('email', 'password'));

        return redirect('/')->with('status', 'Registration successful! Please login.');
    }

    public function showLoginForm()
    {
            if (Auth::check()) {
                return redirect()->route('chat.interface');
            }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/'); // Redirect to chat interface
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}