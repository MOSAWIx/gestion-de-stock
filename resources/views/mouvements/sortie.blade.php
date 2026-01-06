@extends('layouts.app')

@section('content')
<style>
    /* ===== Modern Form Design ===== */
    .form-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        border: none;
    }

    .page-title {
        font-weight: 700;
        letter-spacing: .5px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        padding: 10px 14px;
        border: 1px solid #dee2e6;
    }

    /* Style spécifique pour intégrer Tom Select au design */
    .ts-control {
        border-radius: 12px !important;
        padding: 10px 14px !important;
        border: 1px solid #dee2e6 !important;
        background-color: #fff !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #212529 !important;
        box-shadow: none !important;
    }
    .ts-dropdown {
        border-radius: 12px !important;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .btn-soft {
        background: #f1f3f5;
        color: #495057;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        transition: all 0.2s;
    }

    .btn-soft:hover {
        background: #e9ecef;
    }

    .btn-danger {
        border-radius: 10px;
        padding: 10px 22px;
        font-weight: 600;
    }

    .badge-ref {
        background-color: #eef2ff;
        color: #3b5bdb;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        margin-right: 8px;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Sortie de stock</h4>
        <a href="{{ route('mouvements.index') }}" class="btn btn-soft">
            <i class="fas fa-arrow-left me-2"></i> Retour historique
        </a>
    </div>

    {{-- Messages de notification --}}
    @if(session('success'))
        <div class="alert alert-success rounded-3 border-0 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-3 border-0 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulaire --}}
    <div class="form-card">
        <form action="{{ route('mouvements.sortie.store') }}" method="POST">
            @csrf

            <div class="row">
                {{-- Sélection du Produit --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Produit (Nom ou Référence) *</label>
                    <select id="select-produit-sortie" name="produit_id" class="form-select" required>
                        <option value="">Rechercher un produit...</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}" 
                                    data-reference="{{ $produit->reference }}" 
                                    data-stock="{{ $produit->quantite }}">
                                {{ $produit->reference }} - {{ $produit->name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="stock-hint" class="form-text mt-2" style="display:none;">
                        Stock actuel disponible : <span class="fw-bold text-dark" id="current-stock-val">0</span>
                    </div>
                </div>

                {{-- Quantité --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Quantité à sortir *</label>
                    <input type="number" 
                           name="quantite" 
                           class="form-control" 
                           min="1" 
                           placeholder="Ex: 10" 
                           required>
                </div>

                {{-- Motif --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Motif de la sortie</label>
                    <input type="text" 
                           name="motif" 
                           class="form-control" 
                           placeholder="Vente, Dommage, Utilisation...">
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('mouvements.index') }}" class="btn btn-soft">
                    Annuler
                </a>
                <button type="submit" class="btn btn-danger px-4">
                    Confirmer la sortie
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== "undefined") {
            const selectProduit = new TomSelect("#select-produit-sortie", {
                create: false,
                placeholder: "Tapez le nom ou la référence...",
                allowEmptyOption: true,
                // On définit les champs sur lesquels Tom Select va chercher
                searchField: ["text", "reference"],
                // Personnalisation de l'affichage dans la liste
                render: {
                    option: function(data, escape) {
                        return `<div class="d-flex flex-column py-1">
                                    <div class="fw-bold">
                                        <span class="badge-ref">${escape(data.reference || 'N/A')}</span> 
                                        ${escape(data.text.split(' - ')[1] || data.text)}
                                    </div>
                                    <small class="text-muted">Stock disponible: ${escape(data.stock)}</small>
                                </div>`;
                    },
                    item: function(data, escape) {
                        return `<div><span class="badge-ref">${escape(data.reference || 'N/A')}</span> ${escape(data.text.split(' - ')[1] || data.text)}</div>`;
                    }
                }
            });

            // Afficher le stock sous le champ quand un produit est sélectionné
            selectProduit.on('change', function(value) {
                const stockHint = document.getElementById('stock-hint');
                const stockVal = document.getElementById('current-stock-val');
                
                if (value) {
                    const option = selectProduit.options[value];
                    stockVal.innerText = option.stock;
                    stockHint.style.display = 'block';
                } else {
                    stockHint.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection