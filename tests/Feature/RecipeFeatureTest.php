<?php

use Illuminate\Support\Facades\Http;

test('homepage loads successfully with editorial recipes showcase', function () {
    Http::fake([
        'https://api.spoonacular.com/recipes/random*' => Http::response([
            'recipes' => [
                [
                    'id' => 101,
                    'title' => 'Roasted Tuscan Herb Chicken',
                    'image' => 'https://example.com/chicken.jpg',
                    'readyInMinutes' => 45,
                    'servings' => 4,
                    'summary' => 'A wonderful roasted chicken dish.',
                    'dishTypes' => ['main course'],
                    'diets' => ['gluten free'],
                    'healthScore' => 90,
                ],
                [
                    'id' => 102,
                    'title' => 'Classic Minestrone Soup',
                    'image' => 'https://example.com/soup.jpg',
                    'readyInMinutes' => 30,
                    'servings' => 4,
                    'summary' => 'Rich vegetable broth with pasta and herbs.',
                    'dishTypes' => ['soup'],
                    'diets' => ['vegetarian'],
                    'healthScore' => 85,
                ],
            ],
        ], 200),
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('ATELIER RESEP');
    $response->assertSee('Roasted Tuscan Herb Chicken');
    $response->assertSee('Classic Minestrone Soup');
});

test('search recipes endpoint returns matching results with filters', function () {
    Http::fake([
        'https://api.spoonacular.com/recipes/complexSearch*' => Http::response([
            'results' => [
                [
                    'id' => 201,
                    'title' => 'Avocado Lemon Basil Pasta',
                    'image' => 'https://example.com/pasta.jpg',
                    'readyInMinutes' => 20,
                    'servings' => 2,
                    'summary' => 'Creamy avocado pasta sauce.',
                    'dishTypes' => ['main course'],
                    'diets' => ['vegan'],
                    'healthScore' => 95,
                ],
            ],
            'offset' => 0,
            'number' => 12,
            'totalResults' => 1,
        ], 200),
    ]);

    $response = $this->get('/recipes?q=pasta&type=main+course');

    $response->assertStatus(200);
    $response->assertSee('Avocado Lemon Basil Pasta');
    $response->assertSee('Hasil Pencarian');
});

test('recipe detail page displays ingredients, instructions, and servings adjuster', function () {
    Http::fake([
        'https://api.spoonacular.com/recipes/301/information*' => Http::response([
            'id' => 301,
            'title' => 'Slow Roasted Garlic Salmon',
            'image' => 'https://example.com/salmon.jpg',
            'readyInMinutes' => 25,
            'servings' => 2,
            'healthScore' => 98,
            'sourceName' => 'Epicurean Test Kitchen',
            'sourceUrl' => 'https://example.com/recipe-source',
            'summary' => 'Fresh Atlantic salmon roasted with crushed garlic and dill.',
            'dishTypes' => ['main course'],
            'diets' => ['pescatarian', 'gluten free'],
            'extendedIngredients' => [
                [
                    'name' => 'salmon fillets',
                    'amount' => 2,
                    'unit' => 'fillets',
                ],
                [
                    'name' => 'garlic cloves',
                    'amount' => 4,
                    'unit' => 'cloves',
                ],
            ],
            'analyzedInstructions' => [
                [
                    'steps' => [
                        [
                            'number' => 1,
                            'step' => 'Panaskan oven hingga 180 derajat Celsius.',
                            'ingredients' => [],
                            'equipment' => [],
                        ],
                        [
                            'number' => 2,
                            'step' => 'Lumuri salmon dengan bawang putih halus dan panggang hingga matang lembut.',
                            'ingredients' => [['name' => 'salmon']],
                            'equipment' => [],
                        ],
                    ],
                ],
            ],
            'nutrition' => [
                'nutrients' => [
                    ['name' => 'Calories', 'amount' => 350, 'unit' => 'kcal'],
                    ['name' => 'Protein', 'amount' => 34, 'unit' => 'g'],
                ],
            ],
        ], 200),
        'https://api.spoonacular.com/recipes/301/similar*' => Http::response([], 200),
    ]);

    $response = $this->get('/recipes/301');

    $response->assertStatus(200);
    $response->assertSee('Slow Roasted Garlic Salmon');
    $response->assertSee('salmon fillets');
    $response->assertSee('Panaskan oven hingga 180 derajat Celsius');
    $response->assertSee('data-base-servings="2"', false);
    $response->assertSee('Cetak Lembar Masak');
});

test('saved recipes bookmark collection page loads successfully', function () {
    $response = $this->get('/saved');

    $response->assertStatus(200);
    $response->assertSee('Koleksi Resep Anda');
    $response->assertSee('saved-recipes-grid');
});
