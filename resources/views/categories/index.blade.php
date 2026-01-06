@extends('layouts.app')

@section('content')
<style>
    /* ===== Modern Category List (SAME AS CLIENT) ===== */
    .page-title {
        font-weight: 700;
        letter-spacing: .5px;
    }

    .category-card {
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

    .category-name {
        font-weight: 600;
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

    /* Style ajusté pour Tom Select */
    .ts-control {
        border-radius: 8px !important;
        padding: 8px 12px !important;
        border: 1px solid #dee2e6 !important;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Catégories</h4>

        <a href="{{ route('categories.create') }}" class="btn btn-dark px-4">
            Nouvelle catégorie
        </a>
    </div>

    {{-- Success --}}
    @if(session('success'))
    <div class="alert alert-success rounded-3">
        {{ session('success') }}
    </div>
    @endif

    {{-- SEARCH BAR --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label text-muted small">Recherche par nom</label>
                    {{-- Modification Tom Select ici --}}
                    <select id="category-select" name="name" class="form-control">
                        <option value="">Toutes les catégories...</option>
                        @foreach($allCategoryNames as $name)
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

                <div class="col-md-2">
                    <a href="{{ route('categories.index') }}"
                        class="btn btn-secondary w-100">
                        Réinitialiser
                    </a>
                </div>

            </form>
        </div>
    </div>


    {{-- Card --}}
    <div class="category-card">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="category-name">
                        {{ $cat->name }}
                    </td>

                    <td class="text-end actions-btns">
                        <a href="{{ route('categories.edit', $cat) }}"
                            class="btn btn-sm btn-soft">
                            Modifier
                        </a>

                        <form action="{{ route('categories.destroy', $cat) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Supprimer cette catégorie ?')">
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
                    <td colspan="2" class="text-center text-muted py-4">
                        Aucune catégorie trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if ($categories->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $categories->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Script Tom Select --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== "undefined") {
            new TomSelect("#category-select", {
                create: false,
                allowEmptyOption: true,
                placeholder: "Rechercher une catégorie",
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
    });
</script>
@endsection