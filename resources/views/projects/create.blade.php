@extends('layouts.app')

@section('title', 'Buat Proyek Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Buat Proyek Acara Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Isi rincian perencanaan acara dan konfigurasikan kalender kerjanya.</p>
        </div>
        <a href="{{ route('projects.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 shrink-0 ml-2">
            &larr; Kembali
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
        <form method="POST" action="{{ route('projects.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Acara / Proyek *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Seminar Nasional AI 2026" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Organisasi / Penyelenggara</label>
                <input type="text" name="organization" value="{{ old('organization') }}" placeholder="Contoh: BEM Fakultas Ilmu Komputer" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Perencanaan *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deadline / Hari-H Acara *</label>
                    <input type="date" name="deadline" value="{{ old('deadline', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Proyek *</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planning (Perencanaan)</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Sedang Berjalan)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <label class="block text-xs font-bold text-slate-800">Pengaturan Hari Kerja (Work Calendar)</label>
                <p class="text-xs text-slate-500">Pilih hari-hari yang dihitung sebagai hari kerja aktif untuk perhitungan durasi dan CPM:</p>
                <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 sm:gap-3">
                    @php
                        $days = [
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            7 => 'Minggu'
                        ];
                    @endphp
                    @foreach($days as $val => $dayLabel)
                        <label class="inline-flex items-center space-x-2 text-xs font-medium text-slate-700 bg-white px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-indigo-400">
                            <input type="checkbox" name="calendar_working_days[]" value="{{ $val }}" {{ in_array($val, old('calendar_working_days', [1,2,3,4,5])) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span>{{ $dayLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Acara</label>
                <textarea name="description" rows="3" placeholder="Tuliskan tujuan acara, target peserta, atau catatan penting..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 sm:space-x-3 sm:gap-0">
                <a href="{{ route('projects.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">Batal</a>
                <button type="submit" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition shadow-sm">
                    Simpan Proyek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
