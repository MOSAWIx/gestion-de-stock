@extends('layouts.app')

@section('content')
<style>
    /* ===== Modern Table Style ===== */
    .page-title {
        font-weight: 700;
        letter-spacing: .5px;
    }

    .card-modern {
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

    .badge-soft-success {
        background: #d3f9d8;
        color: #2b8a3e;
    }

    .badge-soft-danger {
        background: #ffe3e3;
        color: #c92a2a;
    }

    .badge-soft-warning {
        background: #fff3bf;
        color: #e67700;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Historique des mouvements de stock</h4>
    </div>

    {{-- FILTER --}}
    <div class="card-modern mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Du</label>
                <input type="date" name="from" class="form-control"
                    value="{{ request('from') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Au</label>
                <input type="date" name="to" class="form-control"
                    value="{{ request('to') }}">
            </div>

            <div class="col-md-3">
                <button class="btn btn-dark w-100">
                    Filtrer
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('mouvements.index') }}"
                    class="btn btn-soft w-100">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card-modern">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Produit</th>
                    <th>Type</th>
                    <th>Qté</th>
                    <th>Avant</th>
                    <th>Après</th>
                    <th>Utilisateur</th>
                    <th>Motif</th>
                </tr>
            </thead>

            <tbody>
                @forelse($mouvements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>

                    <td class="fw-semibold">
                        {{ $m->produit->name ?? '-' }}
                    </td>

                    <td>
                        @if($m->type === 'entree')
                        <span class="badge badge-soft-success">Entrée</span>
                        @elseif($m->type === 'sortie')
                        <span class="badge badge-soft-danger">Sortie</span>
                        @else
                        <span class="badge badge-soft-warning">Ajustement</span>
                        @endif
                    </td>

                    <td>{{ $m->quantite }}</td>
                    <td>{{ $m->stock_avant }}</td>
                    <td>{{ $m->stock_apres }}</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                    <td>{{ $m->motif ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Aucun mouvement trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($mouvements->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $mouvements->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection