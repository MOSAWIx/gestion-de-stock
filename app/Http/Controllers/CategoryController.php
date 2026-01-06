<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // On récupère tous les noms pour le Tom Select
        $allCategoryNames = \App\Models\Category::pluck('name')->unique();

        $query = \App\Models\Category::query();

        // 🔎 SEARCH BY NAME
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $categories = $query
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        return view('categories.index', compact('categories', 'allCategoryNames'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée');
    }
}
