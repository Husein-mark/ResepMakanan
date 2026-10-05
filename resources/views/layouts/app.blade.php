<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-50 text-stone-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Atelier Kuliner — Direktori Resep Pilihan')</title>
    <meta name="description" content="@yield('meta_description', 'Koleksi resep masakan terkurasi dengan panduan langkah, takaran bahan presisi, dan takaran porsi interaktif.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans selection:bg-stone-200 selection:text-stone-900">

    {{-- Top Utility Bar --}}
    <div class="border-b border-stone-200 bg-white text-xs text-stone-600 no-print">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-9 flex items-center justify-between">
            <span class="tracking-widest uppercase font-medium text-[11px] text-stone-500">Jurnal Masak &amp; Panduan Gastronomi</span>
            <div class="flex items-center space-x-6 text-[11px]">
                <span class="text-stone-400">Data bersumber dari Spoonacular</span>
                <a href="{{ route('recipes.saved') }}" class="hover:text-stone-900 transition-colors font-medium">
                    Koleksi Tersimpan (<span id="nav-saved-count">0</span>)
                </a>
            </div>
        </div>
    </div>

    {{-- Main Editorial Navigation --}}
    <header class="border-b border-stone-200 bg-white sticky top-0 z-30 no-print">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Brand Logo --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('recipes.index') }}" class="group inline-flex flex-col">
                    <span class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-stone-900 group-hover:text-stone-700 transition-colors">
                        ATELIER RESEP
                    </span>
                    <span class="text-[10px] tracking-[0.25em] uppercase text-stone-500 -mt-0.5">
                        Koleksi &amp; Takaran Kuliner
                    </span>
                </a>

                {{-- Mobile Saved Link --}}
                <a href="{{ route('recipes.saved') }}" class="sm:hidden text-xs font-medium text-stone-700 border border-stone-300 px-3 py-1.5 rounded hover:bg-stone-100">
                    Koleksi (<span id="nav-saved-count-mobile">0</span>)
                </a>
            </div>

            {{-- Navigation Links --}}
            <nav class="flex items-center space-x-1 sm:space-x-2 text-sm font-medium text-stone-700 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('recipes.index') }}" class="px-3 py-1.5 rounded text-xs uppercase tracking-wider hover:bg-stone-100 transition-colors {{ request()->routeIs('recipes.index') && !request()->has('type') && !request()->has('q') ? 'bg-stone-100 text-stone-900 font-semibold' : '' }}">
                    Semua Resep
                </a>
                <a href="{{ route('recipes.index', ['type' => 'main course']) }}" class="px-3 py-1.5 rounded text-xs uppercase tracking-wider hover:bg-stone-100 transition-colors {{ request('type') === 'main course' ? 'bg-stone-100 text-stone-900 font-semibold' : '' }}">
                    Hidangan Utama
                </a>
                <a href="{{ route('recipes.index', ['type' => 'soup']) }}" class="px-3 py-1.5 rounded text-xs uppercase tracking-wider hover:bg-stone-100 transition-colors {{ request('type') === 'soup' ? 'bg-stone-100 text-stone-900 font-semibold' : '' }}">
                    Sup
                </a>
                <a href="{{ route('recipes.index', ['type' => 'salad']) }}" class="px-3 py-1.5 rounded text-xs uppercase tracking-wider hover:bg-stone-100 transition-colors {{ request('type') === 'salad' ? 'bg-stone-100 text-stone-900 font-semibold' : '' }}">
                    Salad
                </a>
                <a href="{{ route('recipes.index', ['type' => 'dessert']) }}" class="px-3 py-1.5 rounded text-xs uppercase tracking-wider hover:bg-stone-100 transition-colors {{ request('type') === 'dessert' ? 'bg-stone-100 text-stone-900 font-semibold' : '' }}">
                    Penutup
                </a>
                <a href="{{ route('recipes.saved') }}" class="hidden sm:inline-block px-3 py-1.5 rounded text-xs uppercase tracking-wider hover:bg-stone-100 transition-colors {{ request()->routeIs('recipes.saved') ? 'bg-stone-100 text-stone-900 font-semibold' : '' }}">
                    Tersimpan
                </a>
            </nav>
        </div>
    </header>

    {{-- Main Content Slot --}}
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

    {{-- Minimalist Editorial Footer --}}
    <footer class="border-t border-stone-200 bg-white text-stone-600 text-sm py-10 mt-16 no-print">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8 border-b border-stone-100">
                <div>
                    <h3 class="font-serif text-lg font-semibold text-stone-900 mb-2">ATELIER RESEP</h3>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Koleksi resep makanan terpadu dengan takaran presisi, daftar bahan interaktif, dan kalkulator porsi untuk memudahkan persiapan di dapur Anda.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs uppercase font-semibold tracking-wider text-stone-900 mb-3">Kategori Pilihan</h4>
                    <ul class="space-y-1.5 text-xs text-stone-600">
                        <li><a href="{{ route('recipes.index', ['cuisine' => 'Italian']) }}" class="hover:text-stone-900">Masakan Italia</a></li>
                        <li><a href="{{ route('recipes.index', ['cuisine' => 'Asian']) }}" class="hover:text-stone-900">Masakan Asia</a></li>
                        <li><a href="{{ route('recipes.index', ['diet' => 'vegetarian']) }}" class="hover:text-stone-900">Resep Vegetarian</a></li>
                        <li><a href="{{ route('recipes.index', ['diet' => 'gluten free']) }}" class="hover:text-stone-900">Bebas Gluten</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs uppercase font-semibold tracking-wider text-stone-900 mb-3">Informasi</h4>
                    <p class="text-xs text-stone-500 leading-relaxed mb-3">
                        Dirancang dengan kesederhanaan tipografi tanpa elemen visual yang berlebihan agar Anda fokus pada bahan dan langkah memasak.
                    </p>
                    <div class="text-[11px] text-stone-400">
                        Didukung Spoonacular Food API
                    </div>
                </div>
            </div>
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-400 gap-2">
                <span>&copy; {{ date('Y') }} Atelier Resep. Seluruh hak cipta dilindungi.</span>
                <span>Format ramah cetak tersedia di setiap lembar resep.</span>
            </div>
        </div>
    </footer>

    {{-- Notification Toast Container --}}
    <div id="recipe-toast" class="fixed bottom-6 right-6 bg-stone-900 text-white text-xs px-4 py-3 rounded shadow-md transition-all duration-300 transform translate-y-16 opacity-0 pointer-events-none z-50">
        Resep berhasil disimpan.
    </div>

</body>
</html>
