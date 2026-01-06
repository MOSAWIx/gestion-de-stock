@extends('layouts.app')

@section('content')
<style>
    /* ===== Modern Factures List ===== */
    .page-title {
        font-weight: 700;
        letter-spacing: .5px;
    }

    .facture-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        border: none;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-modern thead th {
        border: none;
        color: #6c757d;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .table-modern tbody tr {
        background: #f9fafb;
        border-radius: 14px;
        transition: all .2s ease;
    }

    .table-modern tbody tr:hover {
        background: #f1f3f5;
    }

    .table-modern tbody td {
        border: none;
        padding: 14px 16px;
        vertical-align: middle;
    }

    .btn-soft {
        background: #eef2ff;
        color: #3b5bdb;
        border: none;
        border-radius: 8px;
        padding: 4px 12px;
    }

    .btn-soft:hover {
        background: #e0e7ff;
    }

    /* Style Tom Select pour correspondre au design */
    .ts-control {
        border-radius: 8px !important;
        padding: 8px 12px !important;
        border: 1px solid #dee2e6 !important;
    }

    .btn-soft-warning {
        background: #e7f0ff;
        color: #1c5cff;
        border: none;
        border-radius: 10px;
        padding: 6px 14px;
        font-weight: 500;
    }

    .btn-soft-warning:hover {
        background: #dbe7ff;
    }

    .btn-soft-danger {
        background: #ffe3e3;
        color: #c92a2a;
        border: none;
        border-radius: 10px;
        padding: 6px 14px;
        font-weight: 500;
    }

    .btn-soft-danger:hover {
        background: #ffc9c9;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Factures / Bons de livraison</h4>

        <a href="{{ route('factures.create') }}" class="btn btn-dark px-4">
            Nouvelle facture
        </a>
    </div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="alert alert-success rounded-3">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger rounded-3">
        {{ session('error') }}
    </div>
    @endif

    {{-- 🔍 SEARCH & FILTER --}}
    <form method="GET" class="row g-2 mb-4">

        <div class="col-md-3">
            <select id="select-ref" name="reference" class="form-control">
                <option value="">Référence facture...</option>
                @foreach($allReferences as $ref)
                <option value="{{ $ref }}" {{ request('reference') == $ref ? 'selected' : '' }}>
                    {{ $ref }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select id="select-client" name="client" class="form-control">
                <option value="">Choisir un client...</option>
                @foreach($allClients as $clientName)
                <option value="{{ $clientName }}" {{ request('client') == $clientName ? 'selected' : '' }}>
                    {{ $clientName }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="date"
                name="date_from"
                value="{{ request('date_from') }}"
                class="form-control">
        </div>

        <div class="col-md-2">
            <input type="date"
                name="date_to"
                value="{{ request('date_to') }}"
                class="form-control">
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-dark w-100">
                Filtrer
            </button>
            <a href="{{ route('factures.index') }}" class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </form>

    {{-- Table --}}
    <div class="facture-card">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($factures as $facture)
                <tr>
                    <td>
                        <strong>{{ $facture->reference }}</strong>
                    </td>

                    <td>{{ $facture->client->client ?? '-' }}</td>

                    <td>{{ $facture->created_at->format('d/m/Y') }}</td>

                    <td>
                        {{ number_format($facture->total, 2) }} DH
                    </td>

                    <td class="text-end d-flex justify-content-end gap-2">

                        {{-- 👁️ Voir --}}
                        <a href="{{ route('factures.show', $facture) }}"
                            class="btn btn-sm btn-soft">
                            Voir
                        </a>

                        

                        {{-- 🗑️ Supprimer --}}
                        <button type="button"
                            class="btn btn-sm btn-soft-danger"
                            onclick="confirmDelete('{{ $facture->id }}')">
                            Supprimer
                        </button>

                        <form id="delete-form-{{ $facture->id }}"
                            action="{{ route('factures.destroy', $facture) }}"
                            method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Aucune facture trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if ($factures->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $factures->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Scripts Tom Select --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== "undefined") {
            new TomSelect("#select-ref", {
                create: false,
                allowEmptyOption: true,
                placeholder: "Référence facture",
            });

            new TomSelect("#select-client", {
                create: false,
                allowEmptyOption: true,
                placeholder: "Rechercher un client",
            });
        }
    });

    function confirmDelete(factureId) {
        Swal.fire({
            title: 'Supprimer la facture ?',
            text: "⚠️ Cette action est irréversible et le stock sera rétabli.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fa5252',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + factureId).submit();
            }
        })
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection