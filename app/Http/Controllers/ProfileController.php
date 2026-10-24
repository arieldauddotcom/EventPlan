<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan formulir edit profil pengguna.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Perbarui data profil (nama, username, email, dan password opsional).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'avatar' => ['nullable', 'url', 'max:500'],
            'remove_avatar' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda strip (-) dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email sudah terdaftar di akun lain.',
            'avatar_file.image' => 'File harus berupa gambar.',
            'avatar_file.mimes' => 'Format avatar harus JPG, JPEG, PNG, WEBP, atau SVG.',
            'avatar_file.max' => 'Ukuran avatar maksimal 2MB.',
            'avatar.url' => 'Format URL avatar tidak valid.',
            'current_password.required_with' => 'Kata sandi saat ini harus diisi jika ingin mengganti kata sandi.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'new_password.min' => 'Kata sandi baru minimal :min karakter.',
        ]);

        // Jika user mencoba mengubah password, pastikan current_password benar (jika akun memiliki password)
        if (!empty($validated['new_password'])) {
            if ($user->password && !Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Kata sandi saat ini salah.',
                ])->withInput();
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        $user->email = strtolower($validated['email']);

        // Kelola avatar: upload file prioritas utama, lalu remove, lalu URL eksternal
        if ($request->hasFile('avatar_file')) {
            // Hapus file avatar lama jika ada di local storage
            if ($user->avatar && !str_starts_with($user->avatar, 'http://') && !str_starts_with($user->avatar, 'https://')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar_file')->store('avatars', 'public');
            $user->avatar = $path;
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http://') && !str_starts_with($user->avatar, 'https://')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        } elseif (array_key_exists('avatar', $validated) && !empty($validated['avatar'])) {
            // Jika user memilih URL eksternal dan sebelumnya ada file lokal, hapus file lokal
            if ($user->avatar && !str_starts_with($user->avatar, 'http://') && !str_starts_with($user->avatar, 'https://')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $validated['avatar'];
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
