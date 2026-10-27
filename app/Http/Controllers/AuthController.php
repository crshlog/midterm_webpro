<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back to Freelance Hub!');
        }

        return back()->withErrors(['email' => 'Invalid credentials provided.'])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:140', 'unique:users,email'],
            'role' => ['required', 'in:freelancer,employer'],
            'company_name' => ['required_if:role,employer', 'nullable', 'string', 'max:150'],
            'industry' => ['required_if:role,employer', 'nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        if ($user->isEmployer()) {
            Employer::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'] ?: ($user->name . ' Studio'),
                'industry' => $data['industry'] ?: 'Technology & Design',
                'website' => null,
                'bio' => 'Newly registered employer organization.',
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created successfully! Welcome to Ascend Freelance Hub.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Signed out successfully.');
    }
}
