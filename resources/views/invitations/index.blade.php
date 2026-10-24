@extends('layouts.app')

@section('title', 'Undangan Proyek')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Undangan Proyek</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Daftar undangan kolaborasi proyek acara yang dikirimkan kepada Anda.
            </p>
        </div>
    </div>

    <!-- Daftar Undangan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        @if($invitations->isEmpty())
            <div class="p-8 sm:p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tidak ada undangan masuk</h3>
                <p class="text-xs text-slate-500 mt-1">Anda belum memiliki undangan proyek yang menunggu konfirmasi.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($invitations as $inv)
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/80 transition">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <h4 class="text-sm font-bold text-slate-900">{{ $inv->project->name }}</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ ($inv->role ?? 'member') === 'leader' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                                    Peran: {{ ($inv->role ?? 'member') === 'leader' ? 'Co-Leader' : 'Member' }}
                                </span>
                                @if($inv->isPending())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Menunggu Respon</span>
                                @elseif($inv->isAccepted())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Diterima</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Ditolak</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">
                                Diundang oleh <strong class="text-slate-700">{{ $inv->inviter->name }}</strong> ({{ $inv->inviter->email }}) • {{ $inv->created_at->diffForHumans() }}
                            </p>
                            @if($inv->message)
                                <p class="text-xs italic text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 mt-1">
                                    "{{ $inv->message }}"
                                </p>
                            @endif
                        </div>

                        <!-- Aksi -->
                        <div class="flex items-center space-x-2 shrink-0 w-full sm:w-auto">
                            @if($inv->isPending())
                                <form method="POST" action="{{ route('invitations.accept', $inv) }}" class="flex-1 sm:flex-initial">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs text-center">
                                        Terima & Gabung
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('invitations.decline', $inv) }}" class="flex-1 sm:flex-initial">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-white hover:bg-rose-50 text-rose-600 border border-slate-200 hover:border-rose-300 rounded-xl text-xs font-semibold transition text-center">
                                        Tolak
                                    </button>
                                </form>
                            @elseif($inv->isAccepted())
                                <a href="{{ route('projects.show', $inv->project) }}" class="w-full sm:w-auto text-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                                    Buka Proyek &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $invitations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
