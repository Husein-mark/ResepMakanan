@extends('layouts.app')

@section('title', 'Koleksi Resep Tersimpan — Atelier Resep')
@section('meta_description', 'Daftar resep favorit yang Anda simpan untuk referensi praktis saat memasak di dapur.')

@section('content')
<div class="space-y-8">

    {{-- Header Section --}}
    <div class="border-b border-stone-200 pb-4 flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl sm:text-4xl font-semibold text-stone-900 leading-tight">
                Koleksi Resep Anda
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Disimpan secara lokal di browser Anda untuk akses cepat tanpa memerlukan akun.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <button 
                type="button" 
                onclick="clearAllSavedRecipes()" 
                id="clear-all-btn"
                class="hidden text-xs text-stone-500 hover:text-stone-900 underline underline-offset-4 cursor-pointer"
            >
                Kosongkan Koleksi
            </button>
            <a href="{{ route('recipes.index') }}" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold uppercase tracking-wider rounded transition-colors">
                + Tambah Resep
            </a>
        </div>
    </div>

    {{-- Grid Container for Saved Recipes --}}
    <div id="saved-recipes-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        {{-- Filled dynamically by JavaScript --}}
    </div>

    {{-- Empty State --}}
    <div id="saved-empty-state" class="hidden text-center py-20 px-4 bg-white border border-stone-200 rounded-lg space-y-4">
        <h2 class="font-serif text-2xl font-medium text-stone-800">
            Belum ada resep yang disimpan
        </h2>
        <p class="text-xs text-stone-500 max-w-md mx-auto leading-relaxed">
            Klik tombol &ldquo;+ Simpan&rdquo; pada kartu resep atau halaman panduan masak untuk menambahkan hidangan ke koleksi pribadi Anda.
        </p>
        <div class="pt-3">
            <a href="{{ route('recipes.index') }}" class="inline-block px-5 py-2.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold uppercase tracking-wider rounded transition-colors">
                Mulai Jelajah Resep &rarr;
            </a>
        </div>
    </div>

</div>
@endsection
