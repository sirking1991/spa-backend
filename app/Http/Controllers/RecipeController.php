<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return auth()->user()->recipes;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request)
    {
        $recipe = auth()
            ->user()
            ->recipes()
            ->create($request->validated());

        return response()->json($recipe, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        if ($recipe->user_id !== auth()->user()->id) {
            return response(status:403);
        }

        return response()->json($recipe);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipekRequest $request, Recipe $recipe)
    {
        $recipe->update($request->validated());

        return response()->json($recipe, 201);
    }

    public function complete(Task $recipe) {
        if (!auth()->check() || $recipe->user_id !== auth()->user()->id) {
            return response(status:403);
        }

        $recipe->update(['completed'=>true]);

        return response()->json($recipe, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        if (!auth()->check() || $recipe->user_id !== auth()->user()->id) {
            return response(status:403);
        }

        $recipe->delete();

        return response('');
    }
}
