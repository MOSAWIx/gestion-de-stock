@extends('layouts.app')

@section('content')

<style>
    .bon-container {
        background: #fff;
        padding: 20px; /* Réduit pour gagner de l'espace */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #000;
        max-width: 900px;
        margin: auto;
    }

    /* Header ultra-compact */
    .bon-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #000;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }

    .bon-title {
        font-size: 20px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .bon-section {
        margin-bottom: 15px;
        font-size: 13px;
    }

    /* Style de liste sans tableau */
    .product-list-header {
        display: flex;
        font-weight: bold;
        border-bottom: 1px solid #000;
        padding: 5px 0;
        text-transform: uppercase;
        font-size: 11px;
        background:rgb(255, 255, 255);
    }

    .product-row {
        display: flex;
        border-bottom: 0.5px solid #eee; /* Ligne très fine */
        padding: 3px 0;
        font-size: 12px;
        align-items: center;
    }

    /* Flexbox pour aligner les colonnes sans tableau */
    .col-name { flex: 4; }         /* Produit prend le max d'espace */
    .col-ref  { flex: 2; }         /* Référence */
    .col-qte  { flex: 1; text-align: center; } 
    .col-prix { flex: 1.5; text-align: right; }
    .col-total { flex: 1.5; text-align: right; font-weight: bold; }

    .bon-total-box {
        margin-top: 10px;
        padding-top: 5px;
        border-top: 1px double #000;
        text-align: right;
    }

    .bon-total {
        font-size: 18px;
        font-weight: 800;
    }

    @media print {
        .no-print { display: none; }
        .bon-container { padding: 0; width: 100%; }
        body { background: white; }
    }
</style>

@php
$tva = 0.20;
$totalTTC = 0;
@endphp

<div class="bon-container">

    {{-- ACTIONS --}}
    <div class="no-print mb-3 d-flex justify-content-end gap-2">
        <a href="{{ route('factures.pdf', $facture) }}" class="btn btn-sm btn-secondary">PDF</a>
        <a href="{{ route('factures.index') }}" class="btn btn-sm btn-dark">Retour</a>
    </div>

    {{-- HEADER --}}
    <div class="bon-header">
        <div>
            <div class="bon-title">BON DE LIVRAISON</div>
            <div class="fw-bold">Réf : {{ $facture->reference }}</div>
        </div>
        <div class="text-end small">
            <strong>SKY</strong><br>
            Tél : 0661642727 / 0661722999
        </div>
    </div>

    {{-- INFOS CLIENT --}}
    <div class="row bon-section">
        <div class="col-6">
            <strong>Client :</strong> {{ $facture->client->client }}<br>
            <strong>Tél :</strong> {{ $facture->client->telephone ?? '-' }}
        </div>
        <div class="col-6 text-end">
            <strong>Date :</strong> {{ $facture->created_at->format('d/m/Y') }}
        </div>
    </div>

    {{-- LISTE DES PRODUITS (COMPACTE) --}}
    <div class="product-list">
        <div class="product-list-header">
            <div class="col-ref">Réf</div>
            <div class="col-name ps-1">Produit</div>
            
            <div class="col-qte">Qté</div>
            <div class="col-prix">Prix</div>
            <div class="col-total pe-1">Total </div>
        </div>

        @foreach($facture->items as $item)
            @php
                $prixTTC = $item->prix * (1 + $tva);
                $totalLigneTTC = $prixTTC * $item->quantite;
                $totalTTC += $totalLigneTTC;
            @endphp
            <div class="product-row">
                <div class="col-ref text-muted">{{ $item->produit->reference ?? '-' }}</div>
                <div class="col-name ps-1">{{ $item->produit->name }}</div>
                
                <div class="col-qte">{{ $item->quantite }}</div>
                <div class="col-prix">{{ number_format($prixTTC, 2) }}</div>
                <div class="col-total pe-1">{{ number_format($totalLigneTTC, 2) }}</div>
            </div>
        @endforeach
    </div>

    {{-- TOTAL --}}
    <div class="bon-total-box">
        <span class="me-3">MONTANT À PAYER :</span>
        <span class="bon-total">{{ number_format($totalTTC, 2) }} DH</span>
    </div>

    {{-- SIGNATURE --}}
    <div class="mt-4 row">
        <div class="col-8"></div>
        <div class="col-4 text-center" style="font-size: 11px;">
            <strong>Signature </strong>
            <div style="height:50px; border: 1px dashed #ccc; margin-top:5px;"></div>
        </div>
    </div>
</div>

@endsection