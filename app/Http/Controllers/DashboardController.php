<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Facture;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ======================
        // KPIs
        // ======================
        $kpis = Cache::remember('dashboard_kpis', now()->addHour(), function () {
            return [
                'produits_count' => Produit::count(),
                'quantite_total' => Produit::sum('quantite'),
                'valeur_stock'   => Produit::selectRaw('SUM(quantite * prix_vente) as total')
                    ->value('total'),
            ];
        });

        $produits_count = $kpis['produits_count'];
        $quantite_total = $kpis['quantite_total'];
        $valeur_stock   = $kpis['valeur_stock'];


        // ======================
        // LOW STOCK
        // ======================
        $lowStockProduits = Produit::whereColumn('quantite', '<=', 'stock_min')
            ->orderBy('quantite')
            ->get();

        // ======================
        // PIE / DONUT DATA
        // ======================
        $countRupture = Produit::where('quantite', 0)->count();

        $countFaible = Produit::whereColumn('quantite', '<=', 'stock_min')
            ->where('quantite', '>', 0)
            ->count();

        $countEnStock = Produit::whereColumn('quantite', '>', 'stock_min')->count();

        // ======================
        // DEFAULT CHART (7 jours)
        // ======================
        $filter = $request->get('filter', 'days');

        $labels = [];
        $data   = [];

        // 7 derniers jours (par défaut)
        $days = collect(range(6, 0))->map(
            fn($i) =>
            now()->subDays($i)->format('Y-m-d')
        );

        $ventes = Facture::where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) d, SUM(total) total')
            ->groupBy('d')
            ->pluck('total', 'd');

        foreach ($days as $day) {
            $labels[] = Carbon::parse($day)->format('d/m');
            $data[]   = $ventes[$day] ?? 0;
        }

        $title = 'Ventes - 7 derniers jours';

        return view('dashboard', compact(
            'produits_count',
            'quantite_total',
            'valeur_stock',
            'lowStockProduits',
            'countEnStock',
            'countFaible',
            'countRupture',
            'labels',
            'data',
            'filter',
            'title'
        ));
    }

    // ===================================================
    // AJAX – données du chart (days / weeks / months)
    // ===================================================
    public function ventesData(Request $request)
    {
        $filter = $request->filter ?? 'days';

        $labels = [];
        $data   = [];

        // ======================
        // 7 derniers jours
        // ======================
        if ($filter === 'days') {

            $days = collect(range(6, 0))->map(
                fn($i) =>
                now()->subDays($i)->format('Y-m-d')
            );

            $ventes = Facture::where('created_at', '>=', now()->subDays(6))
                ->selectRaw('DATE(created_at) d, SUM(total) total')
                ->groupBy('d')
                ->pluck('total', 'd');

            foreach ($days as $day) {
                $labels[] = Carbon::parse($day)->format('d/m');
                $data[]   = $ventes[$day] ?? 0;
            }
        }

        // ======================
        // 4 dernières semaines
        // ======================
        elseif ($filter === 'weeks') {

            $startDate = now()->subWeeks(4)->startOfWeek();

            $ventes = Facture::selectRaw('YEAR(created_at) y, WEEK(created_at,1) w, SUM(total) total')
                ->where('created_at', '>=', $startDate)
                ->groupBy('y', 'w')
                ->get();

            // 4 semaines فقط
            foreach (range(3, 0) as $index => $i) {

                $date = now()->subWeeks($i);
                $week = $date->weekOfYear;
                $year = $date->year;

                $found = $ventes->first(
                    fn($v) =>
                    $v->w == $week && $v->y == $year
                );

                // labels: Semaine 1 / 2 / 3 / 4
                $labels[] = 'Semaine ' . ($index + 1);
                $data[]   = $found->total ?? 0;
            }
        }

        // ======================
        // Mois (année courante)
        // ======================
        else {

            $months = collect(range(1, now()->month));

            $ventes = Facture::whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) m, SUM(total) total')
                ->groupBy('m')
                ->pluck('total', 'm');

            foreach ($months as $m) {
                $labels[] = Carbon::create()->month($m)->translatedFormat('M');
                $data[]   = $ventes[$m] ?? 0;
            }
        }
        if ($filter === 'days') {
            $title = 'Ventes - 7 derniers jours';
        } elseif ($filter === 'weeks') {
            $title = 'Ventes - 4 dernières semaines';
        } else {
            $title = 'Ventes par mois';
        }


        return response()->json([
            'labels' => $labels,
            'data'   => $data,
            'title'  => $title
        ]);
    }
}
