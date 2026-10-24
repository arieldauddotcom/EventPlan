@extends('layouts.app')

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
