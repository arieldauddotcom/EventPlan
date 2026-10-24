@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Daftar Proyek Acara</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola seluruh portofolio proyek dan kalender kerja acara Anda.</p>
        </div>
        <div>
            <a href="{{ route('projects.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Proyek Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('projects.index') }}"
              x-data="{
                  searchTimer: null,
                  onSearchInput() {
                      clearTimeout(this.searchTimer);
                      this.searchTimer = setTimeout(() => {
                          $el.submit();
                      }, 500);
                  }
              }"
              x-init="
                  @if(request('search'))
                      if ($refs.projSearchInput) {
                          $refs.projSearchInput.focus();
                          $refs.projSearchInput.setSelectionRange($refs.projSearchInput.value.length, $refs.projSearchInput.value.length);
                      }
                  @endif
              "
              class="w-full flex flex-col sm:flex-row gap-2.5 sm:gap-3">
            <div class="flex-1 relative">
                <input type="text"
                       name="search"
                       x-ref="projSearchInput"
                       @input="onSearchInput()"
                       value="{{ request('search') }}"
                       placeholder="Cari nama proyek atau organisasi..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3">
                <div class="w-full sm:w-44">
                    <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                        <option value="">Semua Peran</option>
                        <option value="leader" {{ request('role') === 'leader' ? 'selected' : '' }}>Dipimpin (Leader)</option>
                        <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Diikuti (Member)</option>
                    </select>
                </div>
                <div class="w-full sm:w-44">
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                        <option value="">Semua Status</option>
                        <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>
            @if(request('search') || request('role') || request('status'))
                <a href="{{ route('projects.index') }}" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 font-semibold text-sm transition text-center flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Project Cards Grid -->
    @if($projects->isEmpty())
        <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
            <p class="text-sm text-slate-500">Tidak ada proyek yang cocok dengan kriteria pencarian.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($projects as $project)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between p-5 hover:border-indigo-300 transition group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $project->status === 'planning' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $project->status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $project->status === 'draft' ? 'bg-slate-100 text-slate-700' : '' }}
                            ">
                                {{ ucfirst($project->status) }}
                            </span>
                            <span class="text-xs text-slate-400">Owner: {{ $project->owner->name }}</span>
                        </div>

                        <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition text-lg line-clamp-1">
                            <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">{{ $project->organization ?? 'Organisasi Umum' }}</p>

                        <p class="text-xs text-slate-600 mt-3 line-clamp-2">
                            {{ $project->description ?: 'Tidak ada deskripsi rinci untuk proyek ini.' }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
                            Deadline: <span class="font-semibold text-slate-700">{{ $project->deadline->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($project->isLeader(auth()->user()))
                                <a href="{{ route('projects.edit', $project) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition" title="Edit Proyek">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            @endif
                            <a href="{{ route('projects.show', $project) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                Buka Detail &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
