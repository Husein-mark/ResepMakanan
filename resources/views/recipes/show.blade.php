@extends('layouts.app')

@section('title', ($recipe['title'] ?? 'Resep') . ' — Atelier Resep')
@section('meta_description', Str::limit(strip_tags($recipe['summary'] ?? ''), 160))

@section('content')
<article class="space-y-10" id="recipe-article" data-base-servings="{{ $recipe['servings'] ?? 4 }}">

    {{-- Breadcrumb & Top Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-stone-200 pb-4 text-xs">
        <nav class="flex items-center space-x-2 text-stone-500">
            <a href="{{ route('recipes.index') }}" class="hover:text-stone-900 transition-colors">Beranda</a>
            <span>/</span>
            <a href="{{ route('recipes.index') }}" class="hover:text-stone-900 transition-colors">Resep</a>
            <span>/</span>
            <span class="text-stone-900 font-medium truncate max-w-xs">{{ $recipe['title'] }}</span>
        </nav>

        {{-- Action Buttons --}}
        <div class="flex items-center space-x-3 no-print">
            <button 
                type="button" 
                onclick="toggleSaveRecipe({{ $recipe['id'] }}, '{{ addslashes($recipe['title']) }}', '{{ $recipe['image'] ?? '' }}', {{ $recipe['readyInMinutes'] ?? 0 }}, {{ $recipe['servings'] ?? 0 }})" 
                id="bookmark-btn-{{ $recipe['id'] }}"
                class="px-3.5 py-1.5 border border-stone-300 rounded hover:border-stone-900 text-stone-800 text-xs font-medium transition-colors cursor-pointer"
            >
                + Simpan Resep
            </button>
            <button 
                type="button" 
                onclick="window.print()" 
                class="px-3.5 py-1.5 border border-stone-300 rounded hover:border-stone-900 text-stone-800 text-xs font-medium transition-colors cursor-pointer"
            >
                Cetak Lembar Masak
            </button>
        </div>
    </div>

    {{-- Header Section --}}
    <header class="space-y-4 max-w-4xl">
        {{-- Tags row --}}
        <div class="flex flex-wrap items-center gap-2 text-xs">
            @if(!empty($recipe['dishTypes']))
                @foreach(array_slice($recipe['dishTypes'], 0, 3) as $type)
                    <span class="bg-stone-100 text-stone-800 px-2.5 py-1 rounded text-[11px] uppercase tracking-wider font-medium">
                        {{ $type }}
                    </span>
                @endforeach
            @endif

            @if(!empty($recipe['diets']))
                @foreach(array_slice($recipe['diets'], 0, 3) as $diet)
                    <span class="border border-stone-300 text-stone-700 px-2.5 py-1 rounded text-[11px] capitalize">
                        {{ $diet }}
                    </span>
                @endforeach
            @endif
        </div>

        {{-- Main Title --}}
        <h1 class="font-serif text-3xl sm:text-5xl font-semibold text-stone-900 leading-tight">
            {{ $recipe['title'] }}
        </h1>

        {{-- Metadata Row --}}
        <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs text-stone-600 pt-2 border-y border-stone-200 py-3">
            <div>
                <span class="text-stone-400 uppercase tracking-widest text-[10px] block">Waktu Masak</span>
                <span class="font-semibold text-stone-900 text-sm">{{ $recipe['readyInMinutes'] ?? '30' }} Menit</span>
            </div>
            <div class="border-l border-stone-200 pl-6">
                <span class="text-stone-400 uppercase tracking-widest text-[10px] block">Porsi Standar</span>
                <span class="font-semibold text-stone-900 text-sm"><span id="header-servings-display">{{ $recipe['servings'] ?? 4 }}</span> Porsi</span>
            </div>
            @if(!empty($recipe['healthScore']))
                <div class="border-l border-stone-200 pl-6">
                    <span class="text-stone-400 uppercase tracking-widest text-[10px] block">Skor Kesehatan</span>
                    <span class="font-semibold text-stone-900 text-sm">{{ $recipe['healthScore'] }}%</span>
                </div>
            @endif
            @if(!empty($recipe['sourceName']))
                <div class="border-l border-stone-200 pl-6">
                    <span class="text-stone-400 uppercase tracking-widest text-[10px] block">Kurasi Oleh</span>
                    <span class="font-medium text-stone-800 text-xs">{{ $recipe['sourceName'] }}</span>
                </div>
            @endif
        </div>
    </header>

    {{-- Recipe Hero Image --}}
    @if(!empty($recipe['image']))
        <div class="rounded-lg overflow-hidden border border-stone-200 bg-stone-100 max-h-[500px]">
            <img 
                src="{{ $recipe['image'] }}" 
                alt="{{ $recipe['title'] }}" 
                class="w-full h-full object-cover object-center"
            >
        </div>
    @endif

    {{-- Main Recipe Grid: Ingredients & Instructions --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 pt-4">

        {{-- Left Sidebar: Interactive Servings & Ingredients --}}
        <div class="lg:col-span-5 space-y-8">
            
            {{-- Ingredients Box --}}
            <div class="bg-white border border-stone-200 rounded-lg p-6 space-y-6 shadow-xs print-break-inside-avoid">
                <div class="flex items-center justify-between border-b border-stone-200 pb-4">
                    <h2 class="font-serif text-xl font-semibold text-stone-900">
                        Bahan Masakan
                    </h2>

                    {{-- Interactive Servings Scaler --}}
                    <div class="flex items-center space-x-2 bg-stone-100 px-2 py-1 rounded no-print">
                        <span class="text-[11px] text-stone-500 font-medium">Porsi:</span>
                        <button 
                            type="button" 
                            onclick="adjustServings(-1)" 
                            class="w-6 h-6 flex items-center justify-center bg-white border border-stone-300 rounded text-stone-700 hover:bg-stone-200 font-mono text-sm leading-none cursor-pointer"
                            aria-label="Kurangi porsi"
                        >&minus;</button>
                        <span id="current-servings" class="font-mono text-xs font-semibold text-stone-900 px-1">
                            {{ $recipe['servings'] ?? 4 }}
                        </span>
                        <button 
                            type="button" 
                            onclick="adjustServings(1)" 
                            class="w-6 h-6 flex items-center justify-center bg-white border border-stone-300 rounded text-stone-700 hover:bg-stone-200 font-mono text-sm leading-none cursor-pointer"
                            aria-label="Tambah porsi"
                        >&plus;</button>
                    </div>
                </div>

                <p class="text-xs text-stone-500 no-print">
                    Centang bahan yang sudah Anda siapkan di dapur untuk memantau proses:
                </p>

                {{-- Ingredients Checklist --}}
                <ul class="space-y-3 text-xs" id="ingredients-list">
                    @forelse($recipe['extendedIngredients'] ?? [] as $ingredient)
                        @php
                            $baseAmount = (float) ($ingredient['amount'] ?? 0);
                            $unit = $ingredient['unit'] ?? '';
                            $name = $ingredient['nameClean'] ?? $ingredient['name'] ?? $ingredient['original'] ?? '';
                        @endphp
                        <li class="flex items-start space-x-3 p-2 rounded hover:bg-stone-50 transition-colors">
                            <input 
                                type="checkbox" 
                                id="ing-{{ $loop->index }}" 
                                class="mt-0.5 rounded border-stone-300 text-stone-900 focus:ring-0 cursor-pointer"
                                onchange="toggleIngredientCheck(this)"
                            >
                            <label for="ing-{{ $loop->index }}" class="flex-1 cursor-pointer leading-relaxed text-stone-700">
                                @if($baseAmount > 0)
                                    <span class="font-semibold text-stone-900 ingredient-amount" data-base="{{ $baseAmount }}">
                                        {{ rtrim(rtrim(number_format($baseAmount, 2), '0'), '.') }}
                                    </span>
                                    <span class="font-medium text-stone-800">{{ $unit }}</span>
                                @endif
                                <span class="ingredient-name">{{ $name }}</span>
                                @if(!empty($ingredient['measures']['metric']['unitLong']) && $unit !== $ingredient['measures']['metric']['unitLong'])
                                    <span class="text-stone-400 text-[11px] block">
                                        ({{ $ingredient['measures']['metric']['amount'] ?? '' }} {{ $ingredient['measures']['metric']['unitLong'] ?? '' }})
                                    </span>
                                @endif
                            </label>
                        </li>
                    @empty
                        <li class="text-stone-500 text-xs">Daftar takaran bahan tidak tersedia secara terpisah.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Nutrition Breakdown --}}
            @if(!empty($recipe['nutrition']['nutrients']))
                @php
                    $desiredNutrients = ['Calories', 'Protein', 'Carbohydrates', 'Fat', 'Fiber', 'Sodium'];
                    $nutrients = collect($recipe['nutrition']['nutrients'])->filter(fn($n) => in_array($n['name'], $desiredNutrients));
                @endphp

                @if($nutrients->isNotEmpty())
                    <div class="bg-white border border-stone-200 rounded-lg p-6 space-y-4 shadow-xs print-break-inside-avoid">
                        <h3 class="font-serif text-lg font-semibold text-stone-900">
                            Informasi Nilai Gizi
                        </h3>
                        <p class="text-xs text-stone-500">Estimasi kandungan gizi per porsi saji:</p>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            @foreach($nutrients as $nutrient)
                                <div class="p-3 bg-stone-50 rounded border border-stone-200">
                                    <div class="text-stone-500 text-[11px]">{{ $nutrient['name'] }}</div>
                                    <div class="text-sm font-semibold text-stone-900 font-mono mt-0.5">
                                        {{ round($nutrient['amount']) }} {{ $nutrient['unit'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>

        {{-- Right Column: Story & Step-by-Step Instructions --}}
        <div class="lg:col-span-7 space-y-8">
            
            {{-- Recipe Summary / Context --}}
            @if(!empty($recipe['summary']))
                <div class="bg-white border border-stone-200 rounded-lg p-6 space-y-3 shadow-xs">
                    <h2 class="font-serif text-xl font-semibold text-stone-900">
                        Catatan Resep
                    </h2>
                    <div class="text-xs sm:text-sm text-stone-600 leading-relaxed space-y-2">
                        {!! strip_tags($recipe['summary'], '<p><a><b><strong><i><em>') !!}
                    </div>
                </div>
            @endif

            {{-- Cooking Instructions --}}
            <div class="bg-white border border-stone-200 rounded-lg p-6 sm:p-8 space-y-6 shadow-xs">
                <div class="flex items-center justify-between border-b border-stone-200 pb-4">
                    <h2 class="font-serif text-2xl font-semibold text-stone-900">
                        Instruksi Memasak
                    </h2>
                    <span class="text-xs text-stone-400 uppercase tracking-widest font-mono">
                        Langkah Bertahap
                    </span>
                </div>

                @php
                    $steps = $recipe['analyzedInstructions'][0]['steps'] ?? [];
                @endphp

                @if(!empty($steps))
                    <ol class="space-y-6">
                        @foreach($steps as $step)
                            <li class="flex items-start space-x-4 print-break-inside-avoid">
                                <span class="w-8 h-8 shrink-0 rounded-full border border-stone-300 bg-stone-100 text-stone-800 font-mono text-xs font-semibold flex items-center justify-center mt-0.5">
                                    {{ $step['number'] ?? $loop->iteration }}
                                </span>
                                <div class="flex-1 space-y-2">
                                    <p class="text-xs sm:text-sm text-stone-800 leading-relaxed">
                                        {{ $step['step'] }}
                                    </p>

                                    {{-- Ingredients or Equipment used in this step --}}
                                    @if(!empty($step['equipment']) || !empty($step['ingredients']))
                                        <div class="flex flex-wrap items-center gap-1.5 pt-1 text-[11px] text-stone-500">
                                            @if(!empty($step['ingredients']))
                                                <span class="text-stone-400">Bahan:</span>
                                                @foreach($step['ingredients'] as $ing)
                                                    <span class="bg-stone-50 border border-stone-200 px-2 py-0.5 rounded text-stone-700">
                                                        {{ $ing['name'] }}
                                                    </span>
                                                @endforeach
                                            @endif
                                            @if(!empty($step['equipment']))
                                                <span class="text-stone-400 ml-2">Alat:</span>
                                                @foreach($step['equipment'] as $eq)
                                                    <span class="bg-stone-50 border border-stone-200 px-2 py-0.5 rounded text-stone-700">
                                                        {{ $eq['name'] }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @elseif(!empty($recipe['instructions']))
                    <div class="text-xs sm:text-sm text-stone-700 leading-relaxed space-y-3">
                        {!! nl2br(e(strip_tags($recipe['instructions']))) !!}
                    </div>
                @else
                    <p class="text-xs text-stone-500">
                        Instruksi langkah terperinci tidak dimuat secara terpisah. Silakan rujuk ringkasan atau tautan sumber resmi di bawah.
                    </p>
                @endif

                {{-- Source Attribution --}}
                @if(!empty($recipe['sourceUrl']))
                    <div class="pt-6 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500 no-print">
                        <span>Sumber resep asli:</span>
                        <a href="{{ $recipe['sourceUrl'] }}" target="_blank" rel="noopener noreferrer" class="text-stone-900 font-medium hover:underline underline-offset-4">
                            Kunjungi Situs Sumber &rarr;
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- Similar Recipes Section --}}
    @if(!empty($similarRecipes))
        <section class="border-t border-stone-200 pt-10 no-print space-y-6">
            <div>
                <h3 class="font-serif text-2xl font-semibold text-stone-900">Resep Serupa Untuk Dicoba</h3>
                <p class="text-xs text-stone-500 mt-1">Rekomendasi hidangan lain dengan karakteristik rasa yang sepadan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($similarRecipes as $similar)
                    <div class="bg-white border border-stone-200 rounded-lg p-5 flex flex-col justify-between hover:border-stone-400 transition-colors">
                        <div class="space-y-2">
                            <span class="text-[10px] text-stone-400 uppercase tracking-widest font-mono">
                                {{ $similar['readyInMinutes'] ?? '30' }} Menit • {{ $similar['servings'] ?? 4 }} Porsi
                            </span>
                            <h4 class="font-serif text-base font-semibold text-stone-900 leading-snug">
                                <a href="{{ route('recipes.show', $similar['id']) }}" class="hover:text-stone-700">
                                    {{ $similar['title'] }}
                                </a>
                            </h4>
                        </div>
                        <div class="pt-4 mt-2 border-t border-stone-100">
                            <a href="{{ route('recipes.show', $similar['id']) }}" class="text-xs text-stone-900 font-medium hover:underline">
                                Buka Resep &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</article>
@endsection
