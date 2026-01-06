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

    .form-control {
        border-radius: 12px;
        padding: 10px 14px;
        border: 1px solid #dee2e6;
    }

    /* Style spécifique pour Tom Select */
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
    }

    .btn-success {
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
        <h4 class="page-title">Entrée de stock</h4>
        <a href="{{ route('mouvements.index') }}" class="btn btn-soft">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm border-0">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm border-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('mouvements.entree.store') }}" method="POST">
            @csrf

            <div class="row">
                {{-- Produit avec Tom Select (Recherche Nom + Réf) --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Produit (Nom ou Référence) *</label>
                    <select id="select-produit" name="produit_id" class="form-select" required>
                        <option value="">Rechercher un produit...</option>
                        @foreach($produits as $produit)
                        <option value="{{ $produit->id }}" 
                                data-reference="{{ $produit->reference }}" 
                                data-stock="{{ $produit->quantite }}">
                            {{ $produit->reference }} - {{ $produit->name }}
                        </option>
                        @endforeach
                    </select>
                    <div id="stock-info" class="form-text mt-2" style="display:none;">
                        Stock actuel : <span class="fw-bold text-primary" id="current-stock-val">0</span>
                    </div>
                </div>

                {{-- Quantité --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Quantité entrée *</label>
                    <input type="number" name="quantite" class="form-control" min="1" placeholder="Saisir la quantité" required>
                </div>

                {{-- Motif --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Motif (optionnel)</label>
                    <input type="text" name="motif" class="form-control" placeholder="Achat fournisseur, Retour...">
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('mouvements.index') }}" class="btn btn-soft">Annuler</a>
                <button type="submit" class="btn btn-success px-4">
                    Valider l'entrée
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script d'initialisation --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== "undefined") {
            const selectEntree = new TomSelect("#select-produit", {
                create: false,
                placeholder: "Tapez le nom ou la référence...",
                allowEmptyOption: true,
                searchField: ["text", "reference"],
                render: {
                    option: function(data, escape) {
                        return `<div class="d-flex flex-column py-1">
                                    <div>
                                        <span class="badge-ref">${escape(data.reference || 'N/A')}</span> 
                                        <strong>${escape(data.text.split(' - ')[1] || data.text)}</strong>
                                    </div>
                                    <small class="text-muted">Stock actuel: ${escape(data.stock)}</small>
                                </div>`;
                    },
                    item: function(data, escape) {
                        return `<div><span class="badge-ref">${escape(data.reference || 'N/A')}</span> ${escape(data.text.split(' - ')[1] || data.text)}</div>`;
                    }
                }
            });

            // Mise à jour de l'affichage du stock
            selectEntree.on('change', function(value) {
                const infoBox = document.getElementById('stock-info');
                const stockVal = document.getElementById('current-stock-val');
                
                if (value) {
                    const data = selectEntree.options[value];
                    stockVal.innerText = data.stock;
                    infoBox.style.display = 'block';
                } else {
                    infoBox.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection