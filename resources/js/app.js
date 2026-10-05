/**
 * Atelier Resep - Client Interactions
 * Pure vanilla JavaScript without heavy external libraries.
 */

const STORAGE_KEY = 'atelier_saved_recipes';

// --- Bookmarking & LocalStorage Utilities ---

function getSavedRecipes() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (e) {
        console.error('Failed to read saved recipes from localStorage', e);
        return [];
    }
}

function persistSavedRecipes(list) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
        updateSavedCountBadge();
    } catch (e) {
        console.error('Failed to persist saved recipes to localStorage', e);
    }
}

function updateSavedCountBadge() {
    const list = getSavedRecipes();
    const count = list.length;
    
    const deskBadge = document.getElementById('nav-saved-count');
    if (deskBadge) {
        deskBadge.textContent = count;
    }

    const mobBadge = document.getElementById('nav-saved-count-mobile');
    if (mobBadge) {
        mobBadge.textContent = count;
    }
}

function showToast(message) {
    const toast = document.getElementById('recipe-toast');
    if (!toast) return;

    toast.textContent = message;
    toast.classList.remove('translate-y-16', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-16', 'opacity-0');
    }, 2400);
}

window.toggleSaveRecipe = function(id, title, image, readyInMinutes, servings) {
    let list = getSavedRecipes();
    const index = list.findIndex(item => Number(item.id) === Number(id));
    const btn = document.getElementById(`bookmark-btn-${id}`);

    if (index > -1) {
        // Remove from bookmarks
        list.splice(index, 1);
        persistSavedRecipes(list);
        if (btn) {
            btn.textContent = '+ Simpan';
            btn.classList.remove('text-stone-900', 'font-semibold');
        }
        showToast('Resep dihapus dari koleksi tersimpan.');
    } else {
        // Add to bookmarks
        list.unshift({
            id: Number(id),
            title: title || 'Resep Kuliner',
            image: image || '',
            readyInMinutes: readyInMinutes || 30,
            servings: servings || 4,
            savedAt: new Date().toISOString()
        });
        persistSavedRecipes(list);
        if (btn) {
            btn.textContent = '✓ Tersimpan';
            btn.classList.add('text-stone-900', 'font-semibold');
        }
        showToast('Resep ditambahkan ke koleksi tersimpan.');
    }

    // If on saved page, re-render the grid
    if (document.getElementById('saved-recipes-grid')) {
        renderSavedRecipesPage();
    }
};

window.clearAllSavedRecipes = function() {
    if (confirm('Apakah Anda yakin ingin mengosongkan seluruh koleksi resep tersimpan?')) {
        localStorage.removeItem(STORAGE_KEY);
        updateSavedCountBadge();
        renderSavedRecipesPage();
        showToast('Koleksi resep berhasil dikosongkan.');
    }
};

function renderSavedRecipesPage() {
    const grid = document.getElementById('saved-recipes-grid');
    const emptyState = document.getElementById('saved-empty-state');
    const clearBtn = document.getElementById('clear-all-btn');
    if (!grid || !emptyState) return;

    const list = getSavedRecipes();

    if (list.length === 0) {
        grid.innerHTML = '';
        emptyState.classList.remove('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    if (clearBtn) clearBtn.classList.remove('hidden');

    grid.innerHTML = list.map(recipe => `
        <article class="bg-white border border-stone-200 rounded-lg overflow-hidden flex flex-col justify-between hover:border-stone-400 transition-colors shadow-xs">
            <div>
                <a href="/recipes/${recipe.id}" class="block overflow-hidden bg-stone-100 aspect-4/3 relative">
                    ${recipe.image ? `
                        <img src="${escapeHtml(recipe.image)}" alt="${escapeHtml(recipe.title)}" class="w-full h-full object-cover" loading="lazy">
                    ` : `
                        <div class="w-full h-full flex items-center justify-center text-stone-400 text-xs uppercase tracking-wider">
                            Foto Tidak Tersedia
                        </div>
                    `}
                    ${recipe.readyInMinutes ? `
                        <div class="absolute bottom-2.5 left-2.5 bg-stone-900/90 text-white text-[11px] px-2.5 py-1 rounded font-mono">
                            ${recipe.readyInMinutes} mnt
                        </div>
                    ` : ''}
                </a>

                <div class="p-5 space-y-2.5">
                    <div class="text-[10px] text-stone-500">
                        <span class="border border-stone-200 px-2 py-0.5 rounded text-stone-700 font-medium">
                            ${recipe.servings || 4} Porsi
                        </span>
                    </div>

                    <h3 class="font-serif text-lg font-semibold text-stone-900 leading-snug line-clamp-2">
                        <a href="/recipes/${recipe.id}" class="hover:text-stone-700 transition-colors">
                            ${escapeHtml(recipe.title)}
                        </a>
                    </h3>
                </div>
            </div>

            <div class="px-5 py-3.5 bg-stone-50/75 border-t border-stone-100 flex items-center justify-between text-xs">
                <button 
                    type="button" 
                    onclick="toggleSaveRecipe(${recipe.id})" 
                    class="text-red-700 hover:text-red-900 font-medium cursor-pointer"
                >
                    Hapus
                </button>
                <a href="/recipes/${recipe.id}" class="text-stone-900 hover:underline underline-offset-4 font-medium">
                    Buka Panduan &rarr;
                </a>
            </div>
        </article>
    `).join('');
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}

function syncBookmarkButtonStates() {
    const list = getSavedRecipes();
    const savedIds = new Set(list.map(item => Number(item.id)));

    savedIds.forEach(id => {
        const btn = document.getElementById(`bookmark-btn-${id}`);
        if (btn) {
            btn.textContent = '✓ Tersimpan';
            btn.classList.add('text-stone-900', 'font-semibold');
        }
    });
}

// --- Interactive Servings Scaler ---

window.adjustServings = function(change) {
    const article = document.getElementById('recipe-article');
    if (!article) return;

    const baseServings = parseFloat(article.dataset.baseServings) || 4;
    const currentDisplay = document.getElementById('current-servings');
    const headerDisplay = document.getElementById('header-servings-display');
    if (!currentDisplay) return;

    let current = parseInt(currentDisplay.textContent.trim(), 10) || baseServings;
    current = Math.max(1, Math.min(24, current + change));

    currentDisplay.textContent = current;
    if (headerDisplay) {
        headerDisplay.textContent = current;
    }

    const ratio = current / baseServings;

    // Recalculate ingredient amounts
    const amountElements = document.querySelectorAll('.ingredient-amount');
    amountElements.forEach(el => {
        const base = parseFloat(el.dataset.base);
        if (!isNaN(base)) {
            const calculated = base * ratio;
            el.textContent = formatAmount(calculated);
        }
    });
};

function formatAmount(val) {
    if (val === 0) return '0';
    // Clean up decimals: 1.5, 0.75, etc.
    const rounded = Math.round(val * 100) / 100;
    if (Number.isInteger(rounded)) {
        return rounded.toString();
    }
    return rounded.toFixed(2).replace(/\.?0+$/, '');
}

// --- Ingredients Checklist Tracker ---

window.toggleIngredientCheck = function(checkbox) {
    const label = checkbox.nextElementSibling;
    if (!label) return;

    if (checkbox.checked) {
        label.classList.add('line-through', 'opacity-50', 'text-stone-400');
    } else {
        label.classList.remove('line-through', 'opacity-50', 'text-stone-400');
    }
};

// --- Initialization on DOMContentLoaded ---

document.addEventListener('DOMContentLoaded', () => {
    updateSavedCountBadge();
    syncBookmarkButtonStates();

    if (document.getElementById('saved-recipes-grid')) {
        renderSavedRecipesPage();
    }
});
