<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\FactureController;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/dashboard/ventes-data', [DashboardController::class, 'ventesData'])
        ->name('dashboard.ventes.data');




    Route::resource('produits', ProduitController::class);
    Route::resource('categories', CategoryController::class);




    Route::resource('clients', ClientController::class);



    Route::get('/mouvements/entree', [MouvementStockController::class, 'createEntree'])
        ->name('mouvements.entree');

    Route::post('/mouvements/entree', [MouvementStockController::class, 'storeEntree'])
        ->name('mouvements.entree.store');


    Route::get('/mouvements/sortie', [MouvementStockController::class, 'createSortie'])
        ->name('mouvements.sortie');

    Route::post('/mouvements/sortie', [MouvementStockController::class, 'storeSortie'])
        ->name('mouvements.sortie.store');

    Route::get('/mouvements', [MouvementStockController::class, 'index'])
        ->name('mouvements.index');

    Route::get('/mouvements/ajustement', [MouvementStockController::class, 'createAjustement'])
        ->name('mouvements.ajustement');

    Route::post('/mouvements/ajustement', [MouvementStockController::class, 'storeAjustement'])
        ->name('mouvements.ajustement.store');

    Route::resource('factures', FactureController::class);

    Route::get(
        '/factures/{facture}/pdf',
        [FactureController::class, 'pdf']
    )->name('factures.pdf');

    
    Route::post('/check-stock', [FactureController::class, 'checkStock'])
        ->name('factures.checkStock');






    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
