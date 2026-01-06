<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\FactureItem;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class FactureController extends Controller
{
    /* ===============================
        LISTE DES FACTURES
    =============================== */
    public function index(Request $request)
    {
        // 1. Récupération des données pour Tom Select
        $allReferences = Facture::pluck('reference')->unique();
        $allClients = Client::pluck('client')->unique();

        // 2. La requête de filtrage
        $factures = Facture::with('client')
            ->when($request->reference, function ($q) use ($request) {
                $q->where('reference', 'like', '%' . $request->reference . '%');
            })
            ->when($request->client, function ($q) use ($request) {
                $q->whereHas('client', function ($c) use ($request) {
                    $c->where('client', 'like', '%' . $request->client . '%');
                });
            })
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        // 3. ENVOI DES VARIABLES (Vérifiez bien cette ligne)
        return view('factures.index', compact('factures', 'allReferences', 'allClients'));
    }

    /* ===============================
        FORM CREATE FACTURE
    =============================== */
    public function create()
    {
        $clients  = Client::orderBy('client')->get();
        $produits = Produit::orderBy('name')->get();

        return view('factures.create', compact('clients', 'produits'));
    }

    /* ===============================
        STORE FACTURE
    =============================== */
    public function store(Request $request)
    {
        // ================= VALIDATION =================
        $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'produits'      => 'required|array|min:1',
            'produits.*'    => 'required|exists:produits,id',
            'prix_ht'       => 'required|array',
            'tva'           => 'required|array',
            'quantites'     => 'required|array',
            'totaux'        => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $produitsLocked = [];

            // ================= CHECK STOCK (SECURITE) =================
            foreach ($request->produits as $i => $produit_id) {

                $produit = Produit::lockForUpdate()->find($produit_id);
                $produitsLocked[$i] = $produit;

                $quantite = (int) $request->quantites[$i];

                if (!$produit) {
                    throw new \Exception('Produit introuvable');
                }

                if ($quantite > $produit->quantite) {
                    throw new \Exception(
                        'Stock insuffisant pour le produit : ' . $produit->name .
                            ' (stock disponible : ' . $produit->quantite . ')'
                    );
                }
            }

            // ================= CREATE FACTURE =================
            $facture = Facture::create([
                'reference' => Facture::generateReference(),
                'client_id' => $request->client_id,
                'total'     => array_sum($request->totaux),
            ]);

            // ================= CREATE FACTURE ITEMS =================
            foreach ($request->produits as $i => $produit_id) {

                $prixHT   = (float) $request->prix_ht[$i];
                $tva      = (float) $request->tva[$i];
                $quantite = (int) $request->quantites[$i];

                // prix TTC unité
                $prixTTC = $prixHT + ($prixHT * $tva / 100);

                // total ligne
                $totalLigne = $prixTTC * $quantite;

                FactureItem::create([
                    'facture_id'  => $facture->id,
                    'produit_id'  => $produit_id,
                    'prix'        => $prixTTC,
                    'quantite'    => $quantite,
                    'total_ligne' => $totalLigne,
                ]);
                // ================= MOUVEMENT STOCK (SORTIE) =================
                $produit = $produitsLocked[$i];

                $stockAvant = $produit->quantite;

                // ================= MOUVEMENT STOCK (SORTIE) =================
                MouvementStock::create([
                    'produit_id'  => $produit_id,
                    'type'        => 'sortie',
                    'quantite'    => $quantite,
                    'stock_avant' => $stockAvant,
                    'stock_apres' => $stockAvant - $quantite,
                    'motif'       => 'Vente - ' . $facture->reference, // 👈 هادي هي
                    'user_id'     => Auth::id(),
                ]);

                // ================= UPDATE STOCK =================
                $produit->decrement('quantite', $quantite);
            }

            DB::commit();
            Cache::forget('dashboard_kpis');

            return redirect()
                ->route('factures.index')
                ->with('success', 'Facture enregistrée avec succès');
        } catch (\Exception $e) {

            DB::rollBack();


            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function destroy(Facture $facture)
    {
        DB::beginTransaction();

        try {

            // 1️⃣ تحميل items + produits
            $facture->load('items.produit');

            foreach ($facture->items as $item) {

                $produit = $item->produit;
                $stockAvant = $produit->quantite;

                // 2️⃣ Mouvement stock (ENTRÉE)
                MouvementStock::create([
                    'produit_id'  => $produit->id,
                    'type'        => 'entree',
                    'quantite'    => $item->quantite,
                    'stock_avant' => $stockAvant,
                    'stock_apres' => $stockAvant + $item->quantite,
                    'motif'       => 'Annulation facture - ' . $facture->reference,
                    'user_id'     => Auth::id(),
                ]);

                // 3️⃣ Update stock
                $produit->increment('quantite', $item->quantite);
            }

            // 4️⃣ Supprimer lignes facture
            $facture->items()->delete();

            // 5️⃣ Supprimer facture
            $facture->delete();

            DB::commit();
            Cache::forget('dashboard_kpis');

            return redirect()
                ->route('factures.index')
                ->with('success', 'Facture supprimée et stock rétabli');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Erreur suppression : ' . $e->getMessage());
        }
    }










    /* ===============================
        SHOW FACTURE
    =============================== */
    public function show(Facture $facture)
    {
        $facture->load('client', 'items.produit');

        return view('factures.show', compact('facture'));
    }
    public function pdf(Facture $facture)

    {



        $facture->load('client', 'items.produit');



        $pdf = Pdf::loadView('factures.show-pdf', compact('facture'))

            ->setPaper('A4', 'portrait');



        return $pdf->download($facture->reference . '.pdf');
    }



    public function checkStock(Request $request)
    {
        $produit = Produit::find($request->produit_id);

        if (!$produit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produit introuvable'
            ]);
        }

        if ($request->quantite > $produit->quantite) {
            return response()->json([
                'status' => 'error',
                'message' => '⚠ Stock insuffisant (stock dispo : ' . $produit->quantite . ')'
            ]);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}
