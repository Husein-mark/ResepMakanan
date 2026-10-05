@extends('layouts.app')

@section('title', $hasFilters ? 'Pencarian Resep — Atelier Resep' : 'Atelier Resep — Direktori Resep Pilihan')

@section('content')
<div class="space-y-12">

    {{-- Editorial Hero & Search Bar --}}
    <section class="text-center max-w-3xl mx-auto pt-4 pb-2 space-y-4">
        <h1 class="font-serif text-3xl sm:text-5xl font-semibold tracking-tight text-stone-900 leading-tight">
            Resep Pilihan &amp; Panduan Kuliner
        </h1>
        <p class="text-stone-600 text-sm sm:text-base leading-relaxed">
            Eksplorasi ribuan hidangan lezat dengan takaran presisi, daftar bahan interaktif, dan panduan memasak bertahap.
        </p>

        {{-- Search Input Form --}}
        <form action="{{ route('recipes.index') }}" method="GET" class="pt-4 max-w-2xl mx-auto">
            <div class="flex flex-col sm:flex-row items-stretch gap-2 bg-white p-2 rounded-lg border border-stone-300 focus-within:border-stone-900 transition-colors shadow-xs">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $query }}" 
                    placeholder="Cari masakan atau bahan... (contoh: pasta, salmon, sup jamur)" 
                    class="flex-1 px-3 py-2 text-sm bg-transparent outline-none text-stone-900 placeholder:text-stone-400"
                >
                @if($selectedType)
                    <input type="hidden" name="type" value="{{ $selectedType }}">
                @endif
                @if($selectedCuisine)
                    <input type="hidden" name="cuisine" value="{{ $selectedCuisine }}">
                @endif
                @if($selectedDiet)
                    <input type="hidden" name="diet" value="{{ $selectedDiet }}">
                @endif
                @if($selectedTime)
                    <input type="hidden" name="time" value="{{ $selectedTime }}">
                @endif

                <div class="flex items-center gap-2">
                    @if($hasFilters)
                        <a href="{{ route('recipes.index') }}" class="px-3 py-2 text-xs font-medium text-stone-500 hover:text-stone-800 transition-colors">
                            Atur Ulang
                        </a>
                    @endif
                    <button type="submit" class="px-5 py-2.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold uppercase tracking-wider rounded transition-colors">
                        Cari Resep
                    </button>
                </div>
            </div>
        </form>

        {{-- Quick Category Pills --}}
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2 text-xs">
            <span class="text-stone-400 uppercase tracking-widest text-[10px] mr-1">Kategori:</span>
            
            <a href="{{ route('recipes.index', array_merge(request()->query(), ['type' => ''])) }}" 
               class="px-3 py-1 rounded-full border transition-colors {{ empty($selectedType) ? 'bg-stone-900 text-white border-stone-900' : 'bg-white text-stone-600 border-stone-300 hover:border-stone-500' }}">
                Semua
            </a>
            <a href="{{ route('recipes.index', array_merge(request()->query(), ['type' => 'main course'])) }}" 
               class="px-3 py-1 rounded-full border transition-colors {{ $selectedType === 'main course' ? 'bg-stone-900 text-white border-stone-900' : 'bg-white text-stone-600 border-stone-300 hover:border-stone-500' }}">
                Hidangan Utama
            </a>
            <a href="{{ route('recipes.index', array_merge(request()->query(), ['type' => 'soup'])) }}" 
               class="px-3 py-1 rounded-full border transition-colors {{ $selectedType === 'soup' ? 'bg-stone-900 text-white border-stone-900' : 'bg-white text-stone-600 border-stone-300 hover:border-stone-500' }}">
                Sup
            </a>
            <a href="{{ route('recipes.index', array_merge(request()->query(), ['type' => 'salad'])) }}" 
               class="px-3 py-1 rounded-full border transition-colors {{ $selectedType === 'salad' ? 'bg-stone-900 text-white border-stone-900' : 'bg-white text-stone-600 border-stone-300 hover:border-stone-500' }}">
                Salad
            </a>
            <a href="{{ route('recipes.index', array_merge(request()->query(), ['type' => 'dessert'])) }}" 
               class="px-3 py-1 rounded-full border transition-colors {{ $selectedType === 'dessert' ? 'bg-stone-900 text-white border-stone-900' : 'bg-white text-stone-600 border-stone-300 hover:border-stone-500' }}">
                Dessert
            </a>
            <a href="{{ route('recipes.index', array_merge(request()->query(), ['type' => 'breakfast'])) }}" 
               class="px-3 py-1 rounded-full border transition-colors {{ $selectedType === 'breakfast' ? 'bg-stone-900 text-white border-stone-900' : 'bg-white text-stone-600 border-stone-300 hover:border-stone-500' }}">
                Sarapan
            </a>
        </div>

        {{-- Secondary Filter Row: Diets & Max Prep Time --}}
        <div class="flex flex-wrap items-center justify-center gap-3 pt-1 text-xs text-stone-600">
            <div class="flex items-center space-x-1.5">
                <span class="text-stone-400 text-[11px]">Diet:</span>
                <select onchange="location = this.value;" class="bg-white border border-stone-300 text-stone-800 text-xs rounded px-2.5 py-1 outline-none">
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['diet' => ''])) }}">Semua Pola Makan</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['diet' => 'vegetarian'])) }}" {{ $selectedDiet === 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['diet' => 'vegan'])) }}" {{ $selectedDiet === 'vegan' ? 'selected' : '' }}>Vegan</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['diet' => 'gluten free'])) }}" {{ $selectedDiet === 'gluten free' ? 'selected' : '' }}>Bebas Gluten</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['diet' => 'ketogenic'])) }}" {{ $selectedDiet === 'ketogenic' ? 'selected' : '' }}>Ketogenik</option>
                </select>
            </div>

            <div class="flex items-center space-x-1.5">
                <span class="text-stone-400 text-[11px]">Waktu:</span>
                <select onchange="location = this.value;" class="bg-white border border-stone-300 text-stone-800 text-xs rounded px-2.5 py-1 outline-none">
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['time' => ''])) }}">Semua Durasi</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['time' => '20'])) }}" {{ $selectedTime == '20' ? 'selected' : '' }}>Di Bawah 20 Menit</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['time' => '35'])) }}" {{ $selectedTime == '35' ? 'selected' : '' }}>Di Bawah 35 Menit</option>
                    <option value="{{ route('recipes.index', array_merge(request()->query(), ['time' => '60'])) }}" {{ $selectedTime == '60' ? 'selected' : '' }}>Di Bawah 60 Menit</option>
                </select>
            </div>
        </div>
    </section>

    {{-- Featured Recipe Banner (Shown only when not actively searching) --}}
    @if(!$hasFilters && $featuredRecipe)
        <section class="border border-stone-200 bg-white rounded-lg overflow-hidden shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                <div class="lg:col-span-7 overflow-hidden bg-stone-100 max-h-96 lg:max-h-none">
                    @if(!empty($featuredRecipe['image']))
                        <img 
                            src="{{ $featuredRecipe['image'] }}" 
                            alt="{{ $featuredRecipe['title'] }}" 
                            class="w-full h-full object-cover object-center"
                            loading="eager"
                        >
                    @else
                        <div class="w-full h-64 lg:h-full bg-stone-200 flex items-center justify-center text-stone-400 text-xs uppercase tracking-widest">
                            Foto Resep
                        </div>
                    @endif
                </div>
                <div class="lg:col-span-5 p-6 sm:p-8 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 text-[11px] uppercase tracking-wider text-stone-500">
                            <span class="font-semibold text-stone-900">Pilihan Editor Hari Ini</span>
                            <span>•</span>
                            <span>{{ $featuredRecipe['readyInMinutes'] ?? '30' }} Menit</span>
                        </div>
                        <h2 class="font-serif text-2xl sm:text-3xl font-semibold text-stone-900 leading-snug">
                            <a href="{{ route('recipes.show', $featuredRecipe['id']) }}" class="hover:text-stone-700 transition-colors">
                                {{ $featuredRecipe['title'] }}
                            </a>
                        </h2>
                        <p class="text-xs text-stone-600 line-clamp-3 leading-relaxed">
                            {{ strip_tags($featuredRecipe['summary'] ?? 'Resep istimewa dengan keseimbangan rasa yang memikat dan proses persiapan yang praktis untuk santapan keluarga Anda.') }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        <div class="text-xs text-stone-500">
                            <span class="font-medium text-stone-900">{{ $featuredRecipe['servings'] ?? 4 }}</span> Porsi
                            @if(!empty($featuredRecipe['healthScore']))
                                <span class="mx-1.5">•</span>
                                Skor Nutrisi: <span class="font-medium text-stone-900">{{ $featuredRecipe['healthScore'] }}%</span>
                            @endif
                        </div>
                        <a href="{{ route('recipes.show', $featuredRecipe['id']) }}" class="inline-flex items-center px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium uppercase tracking-wider rounded transition-colors">
                            Buka Resep &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Results Section Header --}}
    <div class="flex items-baseline justify-between border-b border-stone-200 pb-3">
        <div>
            <h2 class="font-serif text-xl sm:text-2xl font-semibold text-stone-900">
                @if($hasFilters)
                    Hasil Pencarian
                    @if($query)
                        <span class="text-stone-500 font-normal text-lg">untuk &ldquo;{{ $query }}&rdquo;</span>
                    @endif
                @else
                    Daftar Resep Terkini
                @endif
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">
                Menampilkan {{ count($recipes) }} dari total {{ $totalResults }} resep
            </p>
        </div>

        @if($hasFilters)
            <a href="{{ route('recipes.index') }}" class="text-xs text-stone-600 hover:text-stone-900 underline underline-offset-4">
                Hapus Semua Filter
            </a>
        @endif
    </div>

    {{-- Recipe Cards Grid --}}
    @if(count($recipes) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($recipes as $recipe)
                <article class="group bg-white border border-stone-200 rounded-lg overflow-hidden flex flex-col justify-between hover:border-stone-400 transition-colors shadow-xs">
                    <div>
                        {{-- Image Container --}}
                        <a href="{{ route('recipes.show', $recipe['id']) }}" class="block overflow-hidden bg-stone-100 aspect-4/3 relative">
                            @if(!empty($recipe['image']))
                                <img 
                                    src="{{ $recipe['image'] }}" 
                                    alt="{{ $recipe['title'] }}" 
                                    class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-300"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-400 text-xs uppercase tracking-wider">
                                    Foto Tidak Tersedia
                                </div>
                            @endif

                            {{-- Time Badge --}}
                            @if(!empty($recipe['readyInMinutes']))
                                <div class="absolute bottom-2.5 left-2.5 bg-stone-900/90 text-white text-[11px] px-2.5 py-1 rounded font-mono">
                                    {{ $recipe['readyInMinutes'] }} mnt
                                </div>
                            @endif
                        </a>

                        {{-- Body Content --}}
                        <div class="p-5 space-y-2.5">
                            {{-- Diets / Badges --}}
                            <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-stone-500">
                                @if(!empty($recipe['servings']))
                                    <span class="border border-stone-200 px-2 py-0.5 rounded text-stone-700 font-medium">
                                        {{ $recipe['servings'] }} Porsi
                                    </span>
                                @endif
                                @if(!empty($recipe['diets'][0]))
                                    <span class="bg-stone-100 text-stone-700 px-2 py-0.5 rounded capitalize">
                                        {{ $recipe['diets'][0] }}
                                    </span>
                                @elseif(!empty($recipe['dishTypes'][0]))
                                    <span class="bg-stone-100 text-stone-700 px-2 py-0.5 rounded capitalize">
                                        {{ $recipe['dishTypes'][0] }}
                                    </span>
                                @endif
                            </div>

                            {{-- Title --}}
                            <h3 class="font-serif text-lg font-semibold text-stone-900 group-hover:text-stone-700 transition-colors leading-snug line-clamp-2">
                                <a href="{{ route('recipes.show', $recipe['id']) }}">
                                    {{ $recipe['title'] }}
                                </a>
                            </h3>

                            {{-- Summary snippet --}}
                            @if(!empty($recipe['summary']))
                                <p class="text-xs text-stone-600 line-clamp-2 leading-relaxed">
                                    {{ strip_tags($recipe['summary']) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-5 py-3.5 bg-stone-50/75 border-t border-stone-100 flex items-center justify-between text-xs">
                        <button 
                            type="button" 
                            onclick="toggleSaveRecipe({{ $recipe['id'] }}, '{{ addslashes($recipe['title']) }}', '{{ $recipe['image'] ?? '' }}', {{ $recipe['readyInMinutes'] ?? 0 }}, {{ $recipe['servings'] ?? 0 }})" 
                            id="bookmark-btn-{{ $recipe['id'] }}"
                            class="text-stone-600 hover:text-stone-900 font-medium transition-colors cursor-pointer"
                        >
                            + Simpan
                        </button>
                        <a href="{{ route('recipes.show', $recipe['id']) }}" class="text-stone-900 hover:underline underline-offset-4 font-medium">
                            Lihat Panduan &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Simple Clean Pagination --}}
        @if($totalPages > 1)
            <div class="pt-8 flex items-center justify-center space-x-2 text-xs">
                @if($page > 1)
                    <a href="{{ route('recipes.index', array_merge(request()->query(), ['page' => $page - 1])) }}" class="px-3.5 py-2 border border-stone-300 rounded hover:bg-stone-100 font-medium text-stone-800">
                        &larr; Sebelumnya
                    </a>
                @endif

                <span class="px-3.5 py-2 text-stone-600">
                    Halaman <span class="font-semibold text-stone-900">{{ $page }}</span> dari {{ $totalPages }}
                </span>

                @if($page < $totalPages)
                    <a href="{{ route('recipes.index', array_merge(request()->query(), ['page' => $page + 1])) }}" class="px-3.5 py-2 border border-stone-300 rounded hover:bg-stone-100 font-medium text-stone-800">
                        Selanjutnya &rarr;
                    </a>
                @endif
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 px-4 bg-white border border-stone-200 rounded-lg space-y-4">
            <h3 class="font-serif text-xl font-medium text-stone-800">Tidak ada resep yang sesuai</h3>
            <p class="text-xs text-stone-500 max-w-md mx-auto">
                Kami tidak menemukan hidangan yang cocok dengan kriteria pencarian Anda. Coba kurangi filter atau gunakan kata kunci bahan lain.
            </p>
            <div class="pt-2">
                <a href="{{ route('recipes.index') }}" class="inline-block px-4 py-2 bg-stone-900 text-white text-xs uppercase tracking-wider font-semibold rounded hover:bg-stone-800 transition-colors">
                    Lihat Semua Resep
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
