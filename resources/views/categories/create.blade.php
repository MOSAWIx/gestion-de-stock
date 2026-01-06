@extends('layouts.app')

@section('content')
<style>
/* ===== Modern Client Form ===== */
.form-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
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

.form-control {
    border-radius: 12px;
    padding: 10px 14px;
    border: 1px solid #dee2e6;
}

.form-control:focus {
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
        <h4 class="page-title">Nouvelle catégorie</h4>

        <a href="{{ route('categories.index') }}" class="btn btn-soft">
            Retour
        </a>
    </div>

    {{-- Card --}}
    <div class="form-card">

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label">Nom de la catégorie *</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           placeholder="saisir le nom de la catégorie"
                           required>
                </div>

            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('categories.index') }}" class="btn btn-soft">
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
