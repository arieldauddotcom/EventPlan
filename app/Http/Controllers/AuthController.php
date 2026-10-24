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
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')->with('error', 'Integrasi Google OAuth belum dikonfigurasi di file .env.');
        }

        // Gunakan prompt=select_account agar Google selalu menampilkan pilihan akun jika terdapat lebih dari satu sesi akun Google di browser
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Callback dari Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $googleId = (string) $googleUser->getId();
            $googleEmail = strtolower(trim($googleUser->getEmail()));
            $googleName = trim($googleUser->getName() ?? '');

            // 1. Jika pengguna saat ini sedang login dan ingin menautkan akun Google dari profil
            if (Auth::check()) {
                $currentUser = Auth::user();

                // Cek apakah Google ID ini sudah terikat ke akun pengguna lain
                $existingLinked = User::where('google_id', $googleId)
                    ->where('id', '!=', $currentUser->id)
                    ->first();

                if ($existingLinked) {
                    return redirect()->route('profile.edit')
                        ->with('error', "Akun Google ({$googleEmail}) sudah terhubung ke akun '{$existingLinked->name}' ({$existingLinked->email}).");
                }

                $currentUser->update([
                    'google_id' => $googleId,
                    'avatar' => $currentUser->avatar ?: $googleUser->getAvatar(),
                    'email_verified_at' => $currentUser->email_verified_at ?: now(),
                ]);

                return redirect()->route('profile.edit')
                    ->with('success', "Akun Google ({$googleEmail}) berhasil ditautkan ke akun Anda!");
            }

            // 2. Login via Google OAuth: cari pengguna berdasarkan google_id atau email
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $googleEmail)
                ->first();

            if ($user) {
                $updates = [];

                // Tautkan google_id jika belum ada
                if (empty($user->google_id)) {
                    $updates['google_id'] = $googleId;
                }

                // JANGAN PERNAH menimpa nama pengguna jika user sudah memiliki nama (misal nama yang sudah diedit di profil)
                // Hanya gunakan nama dari Google jika nama di database masih kosong atau default
                if (empty($user->name) || $user->name === 'Pengguna EventPlan') {
                    $updates['name'] = $googleName ?: 'Pengguna EventPlan';
                }

                // JANGAN menimpa foto profil jika user sudah upload foto kustom
                if (empty($user->avatar) && !empty($googleUser->getAvatar())) {
                    $updates['avatar'] = $googleUser->getAvatar();
                }

                // Tandai email terverifikasi karena Google sudah memverifikasi akun ini
                if (empty($user->email_verified_at)) {
                    $updates['email_verified_at'] = now();
                }

                if (!empty($updates)) {
                    $user->update($updates);
                }
            } else {
                // Pengguna baru pertama kali mendaftar via Google
                $baseUsername = strtolower(explode('@', $googleEmail)[0]);
                $baseUsername = preg_replace('/[^a-z0-9_-]/', '', $baseUsername) ?: 'user';
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'name' => $googleName ?: 'Pengguna EventPlan',
                    'username' => $username,
                    'email' => $googleEmail,
                    'role' => 'user',
                    'google_id' => $googleId,
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null,
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }

    /**
     * Putuskan tautan akun Google dari profil
     */
    public function unlinkGoogle(Request $request)
    {
        $user = Auth::user();

        // Jika user tidak memiliki password, jangan biarkan unlinking agar tidak terkunci keluar
        if (empty($user->password)) {
            return back()->with('error', 'Harap atur kata sandi terlebih dahulu pada formulir profil sebelum memutuskan tautan Google.');
        }

        $user->update(['google_id' => null]);

        return back()->with('success', 'Tautan akun Google berhasil diputuskan.');
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
