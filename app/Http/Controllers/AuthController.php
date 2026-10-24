<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $googleConfigured = !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret'));

        return view('auth.login', compact('googleConfigured'));
    }

    /**
     * Tampilkan halaman registrasi / signup
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $googleConfigured = !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret'));

        return view('auth.register', compact('googleConfigured'));
    }

    /**
     * Proses pendaftaran akun baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda strip (-) dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal :min karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'role' => 'user', // Aturan 1 & 2: global role 'user'
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        Auth::login($user, true);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil didaftarkan! Selamat datang di EventPlan, ' . $user->name . '.');
    }

    /**
     * Login via Email atau Username dan Password
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('email'));
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => strtolower($loginInput),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Identitas akun atau kata sandi tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id'))) {
            return redirect()->route('login')->with('error', 'Google OAuth belum dikonfigurasi di file .env.');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'name' => $googleUser->getName() ?? $user->name,
                ]);
            } else {
                // Generate username dari email Google
                $baseUsername = strtolower(explode('@', $googleUser->getEmail())[0]);
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Pengguna EventPlan',
                    'username' => $username,
                    'email' => strtolower($googleUser->getEmail()),
                    'role' => 'user', // Aturan 2: otomatis mendapat role 'user'
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null,
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user->name . '!');
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }

    /**
     * Login dev/demo khusus lingkungan lokal
     */
    public function devLogin(Request $request)
    {
        if (!app()->environment('local', 'testing')) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
        ]);

        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name ?: 'Developer Demo User',
                'username' => 'demo',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'))->with('success', 'Berhasil login sebagai ' . $user->name);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
