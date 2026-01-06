<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MouvementStockController extends Controller
{
    /* =========================
     | ENTRÉE DE STOCK
     ========================= */
    public function createEntree()
    {
        // On récupère les produits par ordre alphabétique
        $produits = Produit::orderBy('name')->get();
        return view('mouvements.entree', compact('produits'));
    }

    public function storeEntree(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite'   => 'required|integer|min:1',
            'motif'      => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {

            $produit = Produit::findOrFail($request->produit_id);

            $stockAvant = $produit->quantite;
            $stockApres = $stockAvant + $request->quantite;

            $produit->update([
                'quantite' => $stockApres,
            ]);

            MouvementStock::create([
                'produit_id'  => $produit->id,
                'type'        => 'entree',
                'quantite'    => $request->quantite,
                'stock_avant' => $stockAvant,
                'stock_apres' => $stockApres,
                'motif'       => $request->motif ?? 'Entrée manuelle',
                'user_id'     => Auth::id(),
            ]);
        });
        Cache::forget('dashboard_kpis');

        return redirect()->route('mouvements.entree')
            ->with('success', 'Entrée de stock enregistrée avec succès ✔️');
    }

    /* =========================
     | SORTIE DE STOCK
     ========================= */
    public function createSortie()
    {
        $produits = Produit::orderBy('name')->get();
        return view('mouvements.sortie', compact('produits'));
    }

    public function storeSortie(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite'   => 'required|integer|min:1',
            'motif'      => 'nullable|string|max:255',
        ]);

        $produit = Produit::findOrFail($request->produit_id);

        if ($request->quantite > $produit->quantite) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Quantité demandée supérieure au stock disponible ❌');
        }

        DB::transaction(function () use ($request, $produit) {

            $stockAvant = $produit->quantite;
            $stockApres = $stockAvant - $request->quantite;

            $produit->update([
                'quantite' => $stockApres,
            ]);

            MouvementStock::create([
                'produit_id'  => $produit->id,
                'type'        => 'sortie',
                'quantite'    => $request->quantite,
                'stock_avant' => $stockAvant,
                'stock_apres' => $stockApres,
                'motif'       => $request->motif ?? 'Sortie manuelle',
                'user_id'     => Auth::id(),
            ]);
        });
        Cache::forget('dashboard_kpis');

        return redirect()->route('mouvements.sortie')
            ->with('success', 'Sortie de stock enregistrée avec succès ✔️');
    }

    /* =========================
     | HISTORIQUE + FILTRE DATE
     ========================= */
    public function index(Request $request)
    {
        $query = MouvementStock::with(['produit', 'user'])
            ->orderBy('created_at', 'desc');

        // 🔎 Filtre date début
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        // 🔎 Filtre date fin
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Pagination
        $mouvements = $query->simplePaginate(20);

        return view('mouvements.index', compact('mouvements'));
    }

    /* =========================
     | AJUSTEMENT DE STOCK
     ========================= */
    public function createAjustement()
    {
        $produits = Produit::orderBy('name')->get();
        return view('mouvements.ajustement', compact('produits'));
    }

    public function storeAjustement(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'stock_reel' => 'required|integer|min:0',
            'motif'      => 'required|string|max:255',
        ]);

        $produit = Produit::findOrFail($request->produit_id);

        $stockAvant = $produit->quantite;
        $stockApres = $request->stock_reel;
        $difference = $stockApres - $stockAvant;

        if ($difference == 0) {
            return redirect()->back()
                ->with('error', 'Aucun changement détecté');
        }

        DB::transaction(function () use ($produit, $stockAvant, $stockApres, $difference, $request) {

            $produit->update([
                'quantite' => $stockApres,
            ]);

            MouvementStock::create([
                'produit_id'  => $produit->id,
                'type'        => 'ajustement',
                'quantite'    => abs($difference),
                'stock_avant' => $stockAvant,
                'stock_apres' => $stockApres,
                'motif'       => $request->motif,
                'user_id'     => Auth::id(),
            ]);
        });

        return redirect()->route('mouvements.ajustement')
            ->with('success', 'Ajustement effectué avec succès ✔️');
    }
}
