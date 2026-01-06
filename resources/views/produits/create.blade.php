@extends('layouts.app')

@section('content')
<style>
    /* ===== Modern Product Form ===== */
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
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        padding: 10px 14px;
        border: 1px solid #dee2e6;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: none;
        border-color: #212529;
    }

    .btn-dark {
        border-radius: 10px;
        padding: 8px 22px;
    }

    .btn-soft {
        background: #f1f3f5;
        color: #495057;
        border: none;
        border-radius: 10px;
        padding: 8px 22px;
    }

    .btn-soft:hover {
        background: #e9ecef;
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Nouveau produit</h4>

        <a href="{{ route('produits.index') }}" class="btn btn-soft">
            Retour
        </a>
    </div>

    {{-- Card --}}
    <div class="form-card">
        @if ($errors->any())
        <div class="alert alert-danger">
            Les données contiennent des erreurs, veuillez les corriger.
        </div>
        @endif

        <form action="{{ route('produits.store') }}" method="POST">
            @csrf

            <div class="row g-4">


                <div class="col-md-6">
                    <label class="form-label">Référence *</label>
                    <input type="text"
                        name="reference"
                        class="form-control"
                        placeholder="reférence du produit"
                        value="{{ old('reference') }}"
                        required>
                    @error('reference')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nom du produit *</label>
                    <input type="text"
                        name="name"
                        class="form-control"
                        placeholder="Nom du produit"
                        value="{{ old('name') }}"
                        required>
                </div>



                <div class="col-md-4">
                    <label class="form-label">Quantité *</label>
                    <input type="number"
                        name="quantite"
                        class="form-control"
                        value="{{ old('quantite', 0) }}"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Prix d'achat *</label>
                    <input type="number"
                        step="0.01"
                        name="prix_achat"
                        class="form-control"
                        value="{{ old('prix_achat') }}"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Prix de vente *</label>
                    <input type="number"
                        step="0.01"
                        name="prix_vente"
                        class="form-control"
                        value="{{ old('prix_vente') }}"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Catégorie *</label>
                    <select name="category_id"
                        class="form-select"
                        required>
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Stock minimum *</label>
                    <input type="number"
                        name="stock_min"
                        class="form-control"
                        value="{{ old('stock_min', 10) }}"
                        required>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description"
                        rows="3"
                        class="form-control"
                        placeholder="Description du produit">{{ old('description') }}</textarea>
                </div>

            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('produits.index') }}" class="btn btn-soft">
                    Annuler
                </a>
                <button class="btn btn-dark">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

</div>
@endsection