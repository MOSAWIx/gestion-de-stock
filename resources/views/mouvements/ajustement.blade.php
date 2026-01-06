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

    /* Style Tom Select */
    .ts-control {
        border-radius: 12px !important;
        padding: 10px 14px !important;
        border: 1px solid #dee2e6 !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #212529 !important;
        box-shadow: none !important;
    }

    .btn-soft {
        background: #f1f3f5;
        color: #495057;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
    }

    .btn-warning {
        border-radius: 10px;
        padding: 10px 22px;
        font-weight: 600;
        background-color: #ffc107;
        border: none;
    }

    .badge-ref {
        background-color: #f8f9fa;
        color: #333;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        border: 1px solid #ddd;
        margin-right: 8px;
    }

    .diff-indicator {
        font-size: 0.9rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Ajustement de stock (Inventaire)</h4>
        <a href="{{ route('mouvements.index') }}" class="btn btn-soft">Retour</a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm border-0">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm border-0">{{ session('error') }}</div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('mouvements.ajustement.store') }}">
            @csrf

            <div class="row">
                {{-- Produit avec Recherche Nom + Référence --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Produit à ajuster *</label>
                    <select id="select-produit-ajust" name="produit_id" class="form-select" required>
                        <option value="">Rechercher par nom ou référence...</option>
                        @foreach($produits as $produit)
                        <option value="{{ $produit->id }}" 
                                data-reference="{{ $produit->reference }}" 
                                data-stock="{{ $produit->quantite }}">
                            {{ $produit->reference }} - {{ $produit->name }}
                        </option>
                        @endforeach
                    </select>
                    <div id="stock-theoretical" class="form-text mt-2" style="display:none;">
                        Stock théorique en base : <span class="fw-bold" id="theory-val">0</span>
                    </div>
                </div>

                {{-- Stock Réel --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Stock réel constaté *</label>
                    <input type="number" 
                           id="input-stock-reel"
                           name="stock_reel" 
                           class="form-control" 
                           min="0" 
                           placeholder="Quantité physique en rayon" 
                           required>
                    <div id="diff-box" class="mt-2" style="display:none;">
                        <span id="diff-label" class="diff-indicator"></span>
                    </div>
                </div>

                {{-- Motif --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Motif de l'ajustement *</label>
                    <input type="text" 
                           name="motif" 
                           class="form-control" 
                           placeholder="Ex: Casse, Erreur saisie, Inventaire annuel..." 
                           required>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('mouvements.index') }}" class="btn btn-soft">Annuler</a>
                <button type="submit" class="btn btn-warning text-dark px-4">
                    Valider l'ajustement
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== "undefined") {
            let theoreticalStock = 0;

            const selectAjust = new TomSelect("#select-produit-ajust", {
                create: false,
                placeholder: "Nom ou référence...",
                searchField: ["text", "reference"],
                render: {
                    option: function(data, escape) {
                        return `<div class="py-1">
                                    <span class="badge-ref">${escape(data.reference || 'REF')}</span>
                                    <strong>${escape(data.text.split(' - ')[1] || data.text)}</strong>
                                    <div class="text-muted small">Actuel: ${escape(data.stock)}</div>
                                </div>`;
                    },
                    item: function(data, escape) {
                        return `<div><span class="badge-ref">${escape(data.reference || 'REF')}</span>${escape(data.text.split(' - ')[1] || data.text)}</div>`;
                    }
                }
            });

            const stockTheoreticalBox = document.getElementById('stock-theoretical');
            const theoryValDisplay = document.getElementById('theory-val');
            const inputReel = document.getElementById('input-stock-reel');
            const diffBox = document.getElementById('diff-box');
            const diffLabel = document.getElementById('diff-label');

            // Calcul de la différence en temps réel
            function updateDifference() {
                const reel = parseInt(inputReel.value);
                if (!isNaN(reel) && selectAjust.getValue() !== "") {
                    const diff = reel - theoreticalStock;
                    diffBox.style.display = 'block';
                    
                    if (diff > 0) {
                        diffLabel.innerHTML = `Correction: +${diff} (Surplus)`;
                        diffLabel.style.backgroundColor = '#e7f5ff';
                        diffLabel.style.color = '#1971c2';
                    } else if (diff < 0) {
                        diffLabel.innerHTML = `Correction: ${diff} (Perte)`;
                        diffLabel.style.backgroundColor = '#fff5f5';
                        diffLabel.style.color = '#e03131';
                    } else {
                        diffLabel.innerHTML = `Aucun changement`;
                        diffLabel.style.backgroundColor = '#f8f9fa';
                        diffLabel.style.color = '#495057';
                    }
                } else {
                    diffBox.style.display = 'none';
                }
            }

            selectAjust.on('change', function(value) {
                if (value) {
                    theoreticalStock = parseInt(selectAjust.options[value].stock);
                    theoryValDisplay.innerText = theoreticalStock;
                    stockTheoreticalBox.style.display = 'block';
                    updateDifference();
                } else {
                    stockTheoreticalBox.style.display = 'none';
                    diffBox.style.display = 'none';
                }
            });

            inputReel.addEventListener('input', updateDifference);
        }
    });
</script>
@endsection