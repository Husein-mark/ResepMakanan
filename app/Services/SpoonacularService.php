<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpoonacularService
{
    /**
     * Base URL for the Spoonacular API.
     */
    private string $baseUrl = 'https://api.spoonacular.com';

    /**
     * Create a new service instance.
     */
    public function __construct(
        private ?string $apiKey = null
    ) {
        $this->apiKey = $apiKey ?: (string) config('services.spoonacular.key');
    }

    /**
     * Search recipes with flexible filters and smart caching.
     *
     * @param  array{
     *     query?: string,
     *     type?: string,
     *     cuisine?: string,
     *     diet?: string,
     *     maxReadyTime?: int|string,
     *     number?: int,
     *     offset?: int
     * }  $params
     * @return array{
     *     results: array<int, array<string, mixed>>,
     *     offset: int,
     *     number: int,
     *     totalResults: int
     * }
     */
    public function searchRecipes(array $params = []): array
    {
        $cacheKey = 'spoonacular_search_'.md5(serialize($params));

        return Cache::remember($cacheKey, 86400, function () use ($params): array {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/recipes/complexSearch", array_filter([
                    'apiKey' => $this->apiKey,
                    'query' => $params['query'] ?? null,
                    'type' => $params['type'] ?? null,
                    'cuisine' => $params['cuisine'] ?? null,
                    'diet' => $params['diet'] ?? null,
                    'maxReadyTime' => $params['maxReadyTime'] ?? null,
                    'addRecipeInformation' => 'true',
                    'addRecipeNutrition' => 'true',
                    'number' => $params['number'] ?? 12,
                    'offset' => $params['offset'] ?? 0,
                ]));

                if ($response->successful()) {
                    /** @var array{results: array<int, array<string, mixed>>, offset: int, number: int, totalResults: int} $data */
                    $data = $response->json();

                    return $data;
                }

                Log::warning('Spoonacular search failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (Exception $e) {
                Log::error('Spoonacular search exception', ['message' => $e->getMessage()]);
            }

            return $this->getFallbackSearchResults($params['query'] ?? '');
        });
    }

    /**
     * Retrieve detailed information for a single recipe by ID.
     *
     * @return array<string, mixed>|null
     */
    public function getRecipe(int $id): ?array
    {
        $cacheKey = "spoonacular_recipe_{$id}";

        return Cache::remember($cacheKey, 86400 * 3, function () use ($id): ?array {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/recipes/{$id}/information", [
                    'apiKey' => $this->apiKey,
                    'includeNutrition' => 'true',
                ]);

                if ($response->successful()) {
                    /** @var array<string, mixed> $data */
                    $data = $response->json();

                    return $data;
                }

                Log::warning("Spoonacular recipe fetch failed for ID {$id}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (Exception $e) {
                Log::error("Spoonacular recipe fetch exception for ID {$id}", ['message' => $e->getMessage()]);
            }

            return $this->getFallbackRecipe($id);
        });
    }

    /**
     * Retrieve random featured recipes for the homepage showcase.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRandomRecipes(int $number = 8): array
    {
        $cacheKey = "spoonacular_random_{$number}";

        return Cache::remember($cacheKey, 43200, function () use ($number): array {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/recipes/random", [
                    'apiKey' => $this->apiKey,
                    'number' => $number,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    return $data['recipes'] ?? [];
                }
            } catch (Exception $e) {
                Log::error('Spoonacular random fetch exception', ['message' => $e->getMessage()]);
            }

            return $this->getFallbackRandomRecipes($number);
        });
    }

    /**
     * Retrieve similar recipes for recommendations.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSimilarRecipes(int $id, int $number = 4): array
    {
        $cacheKey = "spoonacular_similar_{$id}_{$number}";

        return Cache::remember($cacheKey, 86400, function () use ($id, $number): array {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/recipes/{$id}/similar", [
                    'apiKey' => $this->apiKey,
                    'number' => $number,
                ]);

                if ($response->successful()) {
                    return $response->json() ?? [];
                }
            } catch (Exception $e) {
                Log::error("Spoonacular similar fetch exception for ID {$id}", ['message' => $e->getMessage()]);
            }

            return [];
        });
    }

    /**
     * Curated fallback search data if API quota is reached or network is unavailable.
     *
     * @return array{results: array<int, array<string, mixed>>, offset: int, number: int, totalResults: int}
     */
    private function getFallbackSearchResults(string $query = ''): array
    {
        $items = [
            [
                'id' => 715415,
                'title' => 'Red Lentil Soup with Fresh Lemon and Greens',
                'image' => 'https://img.spoonacular.com/recipes/715415-556x370.jpg',
                'readyInMinutes' => 35,
                'servings' => 4,
                'healthScore' => 88,
                'summary' => 'A hearty, nutritious soup infused with earthy red lentils, aromatic garlic, and zesty fresh lemon.',
                'dishTypes' => ['soup', 'lunch'],
                'diets' => ['gluten free', 'dairy free', 'vegetarian'],
            ],
            [
                'id' => 716406,
                'title' => 'Pan-Seared Salmon with Herb Chimichurri',
                'image' => 'https://img.spoonacular.com/recipes/716406-556x370.jpg',
                'readyInMinutes' => 25,
                'servings' => 2,
                'healthScore' => 92,
                'summary' => 'Crispy skin salmon fillet topped with a vibrant homemade parsley, oregano, and olive oil chimichurri.',
                'dishTypes' => ['main course', 'dinner'],
                'diets' => ['gluten free', 'pescatarian'],
            ],
            [
                'id' => 658536,
                'title' => 'Roasted Cauliflower and Leek Velouté',
                'image' => 'https://img.spoonacular.com/recipes/658536-556x370.jpg',
                'readyInMinutes' => 45,
                'servings' => 4,
                'healthScore' => 75,
                'summary' => 'Silky smooth roasted cauliflower soup accentuated with caramelized leeks and cold-pressed olive oil.',
                'dishTypes' => ['soup', 'starter'],
                'diets' => ['dairy free', 'vegetarian'],
            ],
            [
                'id' => 644387,
                'title' => 'Garlic Butter Tuscan White Bean Bruschetta',
                'image' => 'https://img.spoonacular.com/recipes/644387-556x370.jpg',
                'readyInMinutes' => 20,
                'servings' => 4,
                'healthScore' => 80,
                'summary' => 'Rustic sourdough toast loaded with creamy cannellini beans, rosemary, roasted garlic, and sea salt.',
                'dishTypes' => ['appetizer', 'snack'],
                'diets' => ['vegetarian'],
            ],
        ];

        if (! empty($query)) {
            $filtered = array_filter($items, function (array $item) use ($query): bool {
                return str_contains(strtolower($item['title']), strtolower($query));
            });
            $items = array_values($filtered);
        }

        return [
            'results' => $items,
            'offset' => 0,
            'number' => count($items),
            'totalResults' => count($items),
        ];
    }

    /**
     * Curated fallback recipe detail if individual API call fails.
     *
     * @return array<string, mixed>
     */
    private function getFallbackRecipe(int $id): array
    {
        return [
            'id' => $id,
            'title' => 'Red Lentil Soup with Fresh Lemon and Greens',
            'image' => 'https://img.spoonacular.com/recipes/715415-556x370.jpg',
            'readyInMinutes' => 35,
            'servings' => 4,
            'healthScore' => 88,
            'sourceName' => 'Culinary Journal',
            'sourceUrl' => 'https://spoonacular.com',
            'summary' => 'A nourishing, comforting red lentil soup packed with rich antioxidants, warming spices, and finished with a splash of fresh lemon juice.',
            'dishTypes' => ['soup', 'lunch', 'dinner'],
            'diets' => ['gluten free', 'dairy free', 'vegetarian'],
            'extendedIngredients' => [
                ['name' => 'red lentils', 'original' => '1 cup dried red lentils, rinsed', 'amount' => 1, 'unit' => 'cup'],
                ['name' => 'olive oil', 'original' => '2 tablespoons extra virgin olive oil', 'amount' => 2, 'unit' => 'tbsp'],
                ['name' => 'yellow onion', 'original' => '1 medium yellow onion, finely diced', 'amount' => 1, 'unit' => 'whole'],
                ['name' => 'garlic cloves', 'original' => '4 cloves garlic, minced', 'amount' => 4, 'unit' => 'cloves'],
                ['name' => 'ground cumin', 'original' => '1 teaspoon ground cumin', 'amount' => 1, 'unit' => 'tsp'],
                ['name' => 'vegetable broth', 'original' => '4 cups low-sodium vegetable broth', 'amount' => 4, 'unit' => 'cups'],
                ['name' => 'baby spinach', 'original' => '2 cups fresh baby spinach or kale', 'amount' => 2, 'unit' => 'cups'],
                ['name' => 'lemon', 'original' => '1 whole fresh lemon, juiced', 'amount' => 1, 'unit' => 'whole'],
                ['name' => 'sea salt and black pepper', 'original' => 'Sea salt and freshly cracked black pepper to taste', 'amount' => 1, 'unit' => 'pinch'],
            ],
            'analyzedInstructions' => [
                [
                    'steps' => [
                        ['number' => 1, 'step' => 'Panaskan minyak zaitun di panci sedang dengan api sedang. Tumis bawang bombay hingga layu dan harum sekitar 4-5 menit.'],
                        ['number' => 2, 'step' => 'Tambahkan bawang putih cincang dan jintan bubuk. Aduk cepat selama 1 menit hingga aroma rempah keluar dengan sedap.'],
                        ['number' => 3, 'step' => 'Masukkan lentil merah yang sudah dibilas bersih dan tuangkan kaldu sayuran. Aduk rata dan biarkan hingga mendidih.'],
                        ['number' => 4, 'step' => 'Kecilkan api, tutup panci dan biarkan mendidih perlahan selama 20-25 menit hingga lentil empuk dan kuah mengental.'],
                        ['number' => 5, 'step' => 'Masukkan daun bayam dan perasan air lemon segar. Bumbui dengan garam laut dan lada hitam secukupnya.'],
                        ['number' => 6, 'step' => 'Sajikan hangat di mangkuk saji dengan sedikit percikan minyak zaitun ekstra di atasnya.'],
                    ],
                ],
            ],
            'nutrition' => [
                'nutrients' => [
                    ['name' => 'Calories', 'amount' => 285, 'unit' => 'kcal'],
                    ['name' => 'Protein', 'amount' => 16, 'unit' => 'g'],
                    ['name' => 'Carbohydrates', 'amount' => 42, 'unit' => 'g'],
                    ['name' => 'Fat', 'amount' => 6, 'unit' => 'g'],
                    ['name' => 'Fiber', 'amount' => 12, 'unit' => 'g'],
                ],
            ],
        ];
    }

    /**
     * Curated fallback random recipes list.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getFallbackRandomRecipes(int $number = 8): array
    {
        return array_slice($this->getFallbackSearchResults()['results'], 0, $number);
    }
}
