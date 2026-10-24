@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Tugas</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui detail, durasi hari kerja, prioritas, dan status tugas.</p>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
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

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tugas / Aktivitas *</label>
                <input type="text" name="name" value="{{ old('name', $task->name) }}" required placeholder="Contoh: Booking Venue Acara" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            @if($otherTasks->isNotEmpty())
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Induk Tugas (Parent Task - Opsional)</label>
                    <select name="parent_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">-- Tanpa Induk (Tugas Utama) --</option>
                        @foreach($otherTasks as $ot)
                            <option value="{{ $ot->id }}" {{ (string)old('parent_id', $task->parent_id) === (string)$ot->id ? 'selected' : '' }}>
                                {{ $ot->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $task->start_date->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi (Hari Kerja Aktif) *</label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $task->duration_days) }}" min="1" max="365" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prioritas *</label>
                    <select name="priority" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low (Rendah)</option>
                        <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium (Sedang)</option>
                        <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High (Tinggi)</option>
                        <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>Urgent (Mendesak)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="todo" {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ old('status', $task->status) === 'review' ? 'selected' : '' }}>Review</option>
                        <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Persentase Selesai (%)</label>
                    <input type="number" name="progress_percentage" value="{{ old('progress_percentage', $task->progress_percentage) }}" min="0" max="100" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Tugas</label>
                <textarea name="description" rows="3" placeholder="Detail teknis tugas..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('projects.show', $project) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition shadow-sm">
                    Simpan Perubahan Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
