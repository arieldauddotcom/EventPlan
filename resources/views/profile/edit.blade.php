@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Profil Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui informasi akun, email, foto profil, dan kata sandi Anda.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 shrink-0 ml-2">
            &larr; Dashboard
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
            <p class="font-semibold mb-1">Harap periksa form Anda:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Integrasi Akun Google -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $user->google_id ? 'bg-emerald-50 border border-emerald-200 text-emerald-600' : 'bg-slate-50 border border-slate-200' }}">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-800">Tautan Akun Google</h3>
                    @if($user->google_id)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                            <svg class="w-3 h-3 mr-0.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Terhubung
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            Belum Terhubung
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if($user->google_id)
                        Akun Anda telah ditautkan dengan Google OAuth. Anda dapat masuk instan tanpa perlu mengetik kata sandi.
                    @else
                        Hubungkan akun Google Anda untuk login cepat dan aman dengan satu kali klik.
                    @endif
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center">
            @if($user->google_id)
                <form method="POST" action="{{ route('auth.google.unlink') }}" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan tautan akun Google dari profil ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto px-3.5 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 border border-rose-200 rounded-xl transition cursor-pointer">
                        Putuskan Tautan Google
                    </button>
                </form>
            @else
                <a href="{{ route('auth.google') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded-xl transition shadow-2xs">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    Hubungkan Akun Google
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white p-4 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bagian Info Profil Dasar -->
            <div class="space-y-4">
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Akun
                </h2>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 pt-2">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-indigo-200 aspect-square shrink-0 shadow-xs" style="border-radius: 9999px;">
                    @else
                        <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xl flex items-center justify-center border-2 border-indigo-200 aspect-square shrink-0 shadow-xs" style="border-radius: 9999px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500">{{ '@' . ($user->username ?? 'user') }} &bull; Role: <span class="capitalize font-medium">{{ $user->role }}</span></p>
                        @if($user->avatar)
                            <label class="inline-flex items-center mt-2 space-x-1.5 text-xs text-rose-600 hover:text-rose-700 cursor-pointer">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span>Hapus foto profil saat ini</span>
                            </label>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Username *</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Hanya huruf, angka, strip, dan underscore.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Foto Profil (File)</label>
                        <input type="file" name="avatar_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1.5 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Maksimal 2MB (JPG, PNG, WEBP, SVG).</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Atau Gunakan URL Gambar Eksternal</label>
                        <input type="url" name="avatar" value="{{ old('avatar', (str_starts_with($user->avatar ?? '', 'http') ? $user->avatar : '')) }}" placeholder="https://example.com/avatar.jpg" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Opsional jika tidak upload file.</p>
                    </div>
                </div>
            </div>

            <!-- Bagian Ubah Password -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Ubah Kata Sandi
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Biarkan kosong jika Anda tidak ingin mengubah kata sandi.</p>
                </div>

                @if($user->password)
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Masukkan kata sandi lama">
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                        <input type="password" name="new_password" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Minimal 6 karakter">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="new_password_confirmation" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Ulangi kata sandi baru">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 sm:space-x-3 sm:gap-0 pt-6 border-t border-slate-100">
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 shadow-sm transition">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
        </form>
    </div>
</div>
@endsection
