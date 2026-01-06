@extends('layouts.app')

@section('content')
<style>
    /* ===== Modern Product List ===== */
    .page-title {
        font-weight: 700;
        letter-spacing: .5px;
    }

    .product-card {
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

    .product-name {
        font-weight: 600;
    }

    .badge-soft-success {
        background: #d3f9d8;
        color: #2b8a3e;
    }

    .badge-soft-warning {
        background: #fff3bf;
        color: #e67700;
    }

    .badge-soft-danger {
        background: #ffe3e3;
        color: #c92a2a;
    }

    .actions-btns a,
    .actions-btns button {
        border-radius: 8px;
        padding: 4px 10px;
    }

    .btn-soft {
        background: #eef2ff;
        color: #3b5bdb;
        border: none;
    }

    .btn-soft:hover {
        background: #e0e7ff;
    }

    .btn-soft-danger {
        background: #ffe3e3;
        color: #c92a2a;
        border: none;
    }

    .btn-soft-danger:hover {
        background: #ffc9c9;
    }
    
    /* Correction pour Tom Select afin qu'il s'intègre parfaitement à votre style */
    .ts-control {
        border-radius: 8px !important;
        padding: 8px 12px !important;
        border: 1px solid #dee2e6 !important;
    }
    /* Uniformisation des Tom Select */
.ts-wrapper .ts-control {
    border-radius: 12px !important; /* Même arrondi que vos inputs */
    padding: 10px 14px !important;  /* Même padding interne */
    border: 1px solid #dee2e6 !important;
    background-color: #ffffff !important;
    min-height: 45px; /* Ajustez selon la hauteur de vos boutons */
    display: flex;
    align-items: center;
}

.ts-wrapper.focus .ts-control {
    border-color: #212529 !important; /* Couleur de focus identique */
    box-shadow: none !important;
}

/* Alignement des labels */
.form-label {
    margin-bottom: 8px;
    font-weight: 600;
}
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Produits</h4>

        <a href="{{ route('produits.create') }}" class="btn btn-dark px-4">
            Nouveau produit
        </a>
    </div>

    {{-- SEARCH BAR --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                {{-- Recherche par Référence --}}
                <div class="col-md-3">
                    <label class="form-label text-muted small">Recherche par référence</label>
                    <select id="ref-select" name="reference" class="form-control">
                        <option value="">recherche par ref</option>
                        @foreach($allReferences as $ref)
                            <option value="{{ $ref }}" {{ request('reference') == $ref ? 'selected' : '' }}>
                                {{ $ref }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Recherche par Nom (Tom Select) --}}
                <div class="col-md-4">
                    <label class="form-label text-muted small">Recherche par nom</label>
                    <select id="name-select" name="name" class="form-control">
                        <option value="">recherche par nom</option>
                        @foreach($allNames as $name)
                            <option value="{{ $name }}" {{ request('name') == $name ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-dark w-100">
                        Rechercher
                    </button>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('produits.index') }}"
                        class="btn btn-secondary w-100">
                        Réinitialiser
                    </a>
                </div>

            </form>
        </div>
    </div>


    {{-- Card --}}
    <div class="product-card">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Qté</th>
                    <th>Prix achat</th>
                    <th>Prix vente</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($produits as $produit)
                <tr>
                    <td>{{ $produit->reference ?? '-' }}</td>

                    <td class="product-name">
                        {{ $produit->name }}
                    </td>

                    <td>{{ $produit->category->name ?? '-' }}</td>

                    <td>{{ $produit->quantite }}</td>

                    <td>{{ number_format($produit->prix_achat,2) }} DH</td>

                    <td>{{ number_format($produit->prix_vente,2) }} DH</td>

                    {{-- Status --}}
                    <td>
                        @if($produit->status === 'en stock')
                        <span class="badge badge-soft-success">En stock</span>
                        @elseif($produit->status === 'faible')
                        <span class="badge badge-soft-warning">Faible</span>
                        @else
                        <span class="badge badge-soft-danger">Rupture</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-end actions-btns">
                        <a href="{{ route('produits.edit', $produit) }}"
                            class="btn btn-sm btn-soft">
                            Modifier
                        </a>

                        <form action="{{ route('produits.destroy', $produit) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Supprimer ce produit ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-soft-danger">
                                Supprimer
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Aucun produit trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($produits->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $produits->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Initialisation de Tom Select --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== "undefined") {
            // Tom Select pour la référence
            new TomSelect("#ref-select", {
                create: false,
                allowEmptyOption: true,
                placeholder: "Choisir référence",
                sortField: { field: "text", direction: "asc" }
            });

            // Tom Select pour le nom
            new TomSelect("#name-select", {
                create: false,
                allowEmptyOption: true,
                placeholder: "Choisir un produit",
                sortField: { field: "text", direction: "asc" }
            });
        }
    });
</script>
@endsection