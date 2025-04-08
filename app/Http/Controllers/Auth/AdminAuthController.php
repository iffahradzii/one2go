<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Admin; // Ensure this model is used
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Show the admin login form
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Handle the admin login
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    // Handle admin logout
    public function logout()
    {
        Session::flush();
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    // Show the admin dashboard
    public function dashboard()
    {
        return view('admin.dashboard'); // Create an admin dashboard view
    }
}

