<?php

namespace App\Http\Controllers;

use App\Services\SpoonacularService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public SpoonacularService $spoonacular
    ) {}

    /**
     * Display a listing of recipes or search results.
     */
    public function index(Request $request): View
    {
        $query = (string) $request->input('q', '');
        $type = (string) $request->input('type', '');
        $cuisine = (string) $request->input('cuisine', '');
        $diet = (string) $request->input('diet', '');
        $maxTime = $request->input('time');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $hasFilters = ! empty($query) || ! empty($type) || ! empty($cuisine) || ! empty($diet) || ! empty($maxTime);

        $featuredRecipe = null;

        if ($hasFilters) {
            $searchData = $this->spoonacular->searchRecipes([
                'query' => $query,
                'type' => $type,
                'cuisine' => $cuisine,
                'diet' => $diet,
                'maxReadyTime' => $maxTime,
                'number' => $perPage,
                'offset' => $offset,
            ]);

            $recipes = $searchData['results'] ?? [];
            $totalResults = $searchData['totalResults'] ?? count($recipes);
        } else {
            // Homepage showcase: load curated random recipes
            $randomRecipes = $this->spoonacular->getRandomRecipes(13);
            if (! empty($randomRecipes)) {
                $featuredRecipe = array_shift($randomRecipes);
                $recipes = array_slice($randomRecipes, 0, 12);
            } else {
                $recipes = [];
            }
            $totalResults = count($recipes);
        }

        $totalPages = (int) ceil($totalResults / $perPage);

        return view('recipes.index', [
            'recipes' => $recipes,
            'featuredRecipe' => $featuredRecipe,
            'totalResults' => $totalResults,
            'query' => $query,
            'selectedType' => $type,
            'selectedCuisine' => $cuisine,
            'selectedDiet' => $diet,
            'selectedTime' => $maxTime,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasFilters' => $hasFilters,
        ]);
    }

    /**
     * Display the specified recipe.
     */
    public function show(int $id): View
    {
        $recipe = $this->spoonacular->getRecipe($id);

        if (! $recipe) {
            throw new NotFoundHttpException('Resep yang Anda cari tidak ditemukan atau telah dihapus.');
        }

        $similarRecipes = $this->spoonacular->getSimilarRecipes($id, 3);

        return view('recipes.show', [
            'recipe' => $recipe,
            'similarRecipes' => $similarRecipes,
        ]);
    }

    /**
     * Display the saved bookmarks page.
     */
    public function saved(): View
    {
        return view('recipes.saved');
    }
}
