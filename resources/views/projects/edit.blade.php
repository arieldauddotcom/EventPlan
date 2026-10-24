@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Edit Proyek</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui informasi, jadwal, dan status proyek acara.</p>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 shrink-0 ml-2">
            &larr; Batal & Kembali
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
        <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Acara / Proyek *</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Organisasi / Penyelenggara</label>
                <input type="text" name="organization" value="{{ old('organization', $project->organization) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deadline *</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Proyek *</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="planning" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>Planning (Perencanaan)</option>
                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active (Sedang Berjalan)</option>
                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                        <option value="draft" {{ old('status', $project->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kalender Kerja *</label>
                    <select name="work_calendar_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach($calendars as $cal)
                            <option value="{{ $cal->id }}" {{ old('work_calendar_id', $project->work_calendar_id) == $cal->id ? 'selected' : '' }}>
                                {{ $cal->name }} ({{ $cal->hours_per_day }}h/hari)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Acara</label>
                <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="pt-4 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 sm:space-x-3 sm:gap-0">
                <a href="{{ route('projects.show', $project) }}" class="w-full sm:w-auto text-center px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">Batal</a>
                <button type="submit" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition shadow-sm">
                    Perbarui Proyek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
