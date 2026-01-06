<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class ProduitController extends Controller
{
    /**
     * دالة حساب Status حسب الكمية والحد الأدنى
     */
    private function calculerStatus($quantite, $stock_min)
    {
        if ($quantite == 0) {
            return "rupture"; // نفاذ من المخزون
        }

        if ($quantite <= $stock_min) {
            return "faible"; // مخزون ضعيف
        }

        return "en stock"; // مخزون كافٍ
    }


    /**
     *Afficher la liste des produits
     */
    public function index(Request $request)
    {
        // 1. On récupère les références pour le 1er Tom Select
        $allReferences = Produit::whereNotNull('reference')
            ->distinct()
            ->pluck('reference');

        // 2. On récupère les noms pour le 2ème Tom Select (C'EST CE QUI MANQUE)
        $allNames = Produit::distinct()
            ->pluck('name');

        $query = Produit::with('category');

        // Filtrage par référence
        if ($request->filled('reference')) {
            $query->where('reference', $request->reference);
        }

        // Filtrage par nom
        if ($request->filled('name')) {
            $query->where('name', $request->name);
        }

        $produits = $query
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        // 3. On passe BIEN les deux variables à la vue
        return view('produits.index', compact('produits', 'allReferences', 'allNames'));
    }




    /**
     * Afficher le formulaire d'ajout
     */
    public function create()
    {
        $categories = Category::all();
        return view('produits.create', compact('categories'));
    }



    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name' => 'required|string',
                'reference'   => 'required|string|max:255|unique:produits,reference',
                'quantite' => 'required|integer',
                'prix_achat' => 'nullable|numeric',
                'prix_vente' => 'nullable|numeric',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
                'stock_min' => 'nullable|integer',
            ],
            [
                'reference.unique' => 'Cette référence a déjà été utilisée, vous devez en saisir une autre.',
                'reference.required' => 'La référence du produit est essentielle.',
            ]
        );

        // Default stock_min si utilisateur مبغاش يحط القيمة
        $data['stock_min'] = $request->input('stock_min', 10);

        // حساب status قبل الإنشاء
        $data['status'] = $this->calculerStatus($data['quantite'], $data['stock_min']);

        Produit::create($data);
        Cache::forget('dashboard_kpis');

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé');
    }



    /**
     * Afficher la page de modification
     */
    public function edit(Produit $produit)
    {
        $categories = Category::all();
        return view('produits.edit', compact('produit', 'categories'));
    }



    /**
     * Modifier un produit
     */
    public function update(Request $request, Produit $produit)
    {
        $data = $request->validate(
            [
                'name' => 'required|string',
                'reference'   => 'required|string|max:255|unique:produits,reference,' . $produit->id,
                'quantite' => 'required|integer',
                'prix_achat' => 'nullable|numeric',
                'prix_vente' => 'nullable|numeric',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
                'stock_min' => 'required|integer',
            ],
            [
                'reference.unique' => 'Cette référence a déjà été utilisée, vous devez en saisir une autre.',
                'reference.required' => 'La référence du produit est essentielle.',
            ]
        );

        // حساب status من جديد لأن الكمية أو stock_min تغيرو
        $data['status'] = $this->calculerStatus($data['quantite'], $data['stock_min']);

        $produit->update($data);
        Cache::forget('dashboard_kpis');

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès');
    }



    /**
     * Supprimer un produit
     */
    public function destroy(Produit $produit)
    {
        $produit->delete();
        Cache::forget('dashboard_kpis');

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
