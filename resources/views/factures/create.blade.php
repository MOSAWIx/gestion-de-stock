@extends('layouts.app')

@section('content')

<style>
    .btn-soft-primary {
        background: #eef2ff;
        color: #3b5bdb;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        font-weight: 500;
    }

    .btn-soft-danger {
        background: #ffe3e3;
        color: #c92a2a;
        border: none;
        border-radius: 10px;
        padding: 6px 14px;
        font-weight: 500;
    }

    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .table-responsive {
        overflow: visible !important;
    }

    .ts-dropdown {
        z-index: 9999 !important;
        position: absolute !important;
    }

    /* Style pour la bordure rouge sur TomSelect en cas d'erreur */
    .is-invalid+.ts-wrapper .ts-control {
        border-color: #dc3545 !important;
    }

    .loading-stock {
        opacity: 0.5;
    }
</style>

<div class="container">
    <h3 class="mb-4">Nouvelle Facture</h3>

    <form action="{{ route('factures.store') }}" method="POST" id="invoice-form">
        @csrf

        {{-- CLIENT --}}
        <div class="card mb-4">
            <div class="card-body">
                <label class="form-label fw-bold">Client</label>
                <select name="client_id" id="client_id" class="form-select" required>
                    <option value="">Rechercher un client...</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name ?? $client->client }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- PRODUITS --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <strong class="text-secondary">Détails des articles</strong>
                <button type="button" id="addProduit" class="btn btn-soft-primary">
                    <i class="fas fa-plus me-1"></i> Ajouter une ligne
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="table-produits">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Produit / Référence</th>
                                <th>Prix HT</th>
                                <th style="width: 10%;">TVA %</th>
                                <th>Prix TTC</th>
                                <th style="width: 12%;">Qté</th>
                                <th>Total TTC</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="produit-row" data-stock-ok="false">
                                <td>
                                    <select name="produits[]" class="produit-select" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach($produits as $p)
                                        <option value="{{ $p->id }}" data-prix="{{ $p->prix_vente }}">
                                            {{ $p->reference }} - {{ $p->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" class="form-control prix_ht" name="prix_ht[]" readonly></td>
                                <td><input type="number" class="form-control tva" name="tva[]" value="20"></td>
                                <td><input type="number" step="0.01" class="form-control prix_ttc" name="prix_ttc[]" readonly></td>
                                <td>
                                    <input type="number" class="form-control quantite" name="quantites[]" value="1" min="1">
                                    <small class="text-danger stock-error d-none" style="font-size: 0.7rem;"></small>
                                </td>
                                <td><input type="number" step="0.01" class="form-control total" name="totaux[]" readonly></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-soft-danger supprimer">Supprimer</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RÉSUMÉ --}}
        <div class="mt-4 row justify-content-end">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 shadow-sm text-end">
                    <span class="fw-bold me-2">Total TTC :</span>
                    <input type="number" id="total_facture" class="form-control d-inline-block w-50 border-0 bg-transparent fw-bold text-end fs-5" step="0.01" readonly value="0.00">
                    <span class="fw-bold">DH</span>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-5">
            <button type="submit" id="btn-submit" class="btn btn-primary px-5 py-2" disabled>
                Enregistrer la Facture
            </button>
        </div>
    </form>
</div>

{{-- SCRIPTS --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
    function debounce(fn, delay = 100) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    }
    document.addEventListener('DOMContentLoaded', function() {

        const rowTemplate = document.querySelector('.produit-row').cloneNode(true);

        new TomSelect('#client_id', {
            placeholder: 'Chercher...',
            allowEmptyOption: true,
            dropdownParent: 'body'
        });

        // --- NOUVELLE FONCTION : Vérifier les doublons ---
        function checkDuplicateProduct(row) {
            const currentSelect = row.querySelector('.produit-select');
            const currentValue = currentSelect.value;
            const allSelects = document.querySelectorAll('.produit-select');
            const errorBox = row.querySelector('.stock-error');

            let isDuplicate = false;

            if (currentValue !== "") {
                allSelects.forEach(select => {
                    if (select !== currentSelect && select.value === currentValue) {
                        isDuplicate = true;
                    }
                });
            }

            if (isDuplicate) {
                currentSelect.classList.add('is-invalid');
                errorBox.innerText = "Produit déjà sélectionné !";
                errorBox.classList.remove('d-none');
                row.dataset.stockOk = "false";
                toggleSubmitButton();
                return true;
            } else {
                currentSelect.classList.remove('is-invalid');
                // On ne cache l'errorBox que si elle ne contient pas déjà une erreur de stock
                if (errorBox.innerText === "Produit déjà sélectionné !") {
                    errorBox.classList.add('d-none');
                }
                return false;
            }
        }

        function initProductSelect(element) {
            if (element.tomselect) element.tomselect.destroy();

            return new TomSelect(element, {
                placeholder: 'Chercher un produit...',
                allowEmptyOption: true,
                dropdownParent: 'body',
                onChange: function(value) {
                    let row = element.closest('.produit-row');

                    // 1. Vérifier si doublon d'abord
                    if (checkDuplicateProduct(row)) {
                        row.querySelector('.prix_ht').value = '';
                        calculerRow(row);
                        return;
                    }

                    let selectedOption = element.querySelector('option[value="' + value + '"]');
                    if (selectedOption && value !== "") {
                        row.querySelector('.prix_ht').value = selectedOption.getAttribute('data-prix');
                        calculerRow(row);
                        checkStockAjax(row);
                    } else {
                        row.querySelector('.prix_ht').value = '';
                        row.dataset.stockOk = "false";
                        calculerRow(row);
                        toggleSubmitButton();
                    }
                }
            });
        }

        function calculerRow(row) {
            const ht = parseFloat(row.querySelector('.prix_ht').value || 0);
            const tva = parseFloat(row.querySelector('.tva').value || 0);
            const qte = parseFloat(row.querySelector('.quantite').value || 0);
            const ttcUnit = ht * (1 + (tva / 100));
            row.querySelector('.prix_ttc').value = ttcUnit.toFixed(2);
            row.querySelector('.total').value = (ttcUnit * qte).toFixed(2);
            calculerFacture();
        }

        function calculerFacture() {
            let grandTotal = 0;
            document.querySelectorAll('.total').forEach(input => grandTotal += parseFloat(input.value || 0));
            document.getElementById('total_facture').value = grandTotal.toFixed(2);
        }

        function toggleSubmitButton() {
            const rows = document.querySelectorAll('.produit-row');
            const allOk = Array.from(rows).every(row => row.dataset.stockOk === "true");
            document.getElementById('btn-submit').disabled = !allOk || rows.length === 0;
        }

        async function checkStockAjax(row) {
            
            const produitId = row.querySelector('.produit-select').value;
            const quantite = row.querySelector('.quantite').value;
            const errorBox = row.querySelector('.stock-error');
            const qteInput = row.querySelector('.quantite');
            const selectElement = row.querySelector('.produit-select');
            


            // Si c'est un doublon, on ne check pas le stock
            if (selectElement.classList.contains('is-invalid')) return;

            if (!produitId || quantite <= 0) return;

            qteInput.classList.add('loading-stock');
            try {
                const response = await fetch("{{ route('factures.checkStock') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        produit_id: produitId,
                        quantite: quantite
                    })
                });
                const data = await response.json();

                if (data.status === 'error') {
                    errorBox.innerText = data.message;
                    errorBox.classList.remove('d-none');
                    qteInput.classList.add('is-invalid');
                    row.dataset.stockOk = "false";
                } else {
                    errorBox.classList.add('d-none');
                    qteInput.classList.remove('is-invalid');
                    row.dataset.stockOk = "true";
                }
            } catch (err) {
                console.error(err);
            } finally {
                qteInput.classList.remove('loading-stock');
                toggleSubmitButton();
            }
        }
        const checkStockDebounced = debounce(checkStockAjax, 100);

        function bindRowEvents(row) {
            row.querySelector('.tva').addEventListener('input', () => calculerRow(row));
            row.querySelector('.quantite').addEventListener('input', () => {
                calculerRow(row);
                checkStockDebounced(row);
            });
            row.querySelector('.supprimer').addEventListener('click', function() {
                if (document.querySelectorAll('.produit-row').length > 1) {
                    const sel = row.querySelector('.produit-select');
                    if (sel.tomselect) sel.tomselect.destroy();
                    row.remove();

                    // Re-vérifier les doublons sur les lignes restantes après suppression
                    document.querySelectorAll('.produit-row').forEach(r => checkDuplicateProduct(r));

                    calculerFacture();
                    toggleSubmitButton();
                }
            });
        }

        // Initialisation
        initProductSelect(document.querySelector('.produit-select'));
        bindRowEvents(document.querySelector('.produit-row'));

        // Ajout de ligne
        document.getElementById('addProduit').addEventListener('click', function() {
            const newRow = rowTemplate.cloneNode(true);
            newRow.querySelectorAll('input').forEach(i => i.value = '');
            newRow.querySelector('.tva').value = 20;
            newRow.querySelector('.quantite').value = 1;
            newRow.dataset.stockOk = "false";

            document.querySelector('#table-produits tbody').appendChild(newRow);

            initProductSelect(newRow.querySelector('.produit-select'));
            bindRowEvents(newRow);
            toggleSubmitButton();
        });
    });
</script>
@endsection