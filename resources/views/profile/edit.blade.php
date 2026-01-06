@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="bg-primary text-white p-3 rounded-3 shadow-sm">
            <i class="bi bi-person-circle fs-3"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">Mon Profil</h2>
            <p class="text-muted mb-0">Gérez vos informations personnelles et la sécurité de votre compte</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-primary"></i> Informations du Profil
                    </h5>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock text-warning"></i> Sécurité du Compte
                    </h5>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card shadow-sm border-0 border-start border-danger border-4 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle"></i> Zone de Danger
                    </h5>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style pour uniformiser les formulaires avec votre dashboard */
    .form-label {
        font-weight: 600;
        color: #4b5563;
        font-size: 0.9rem;
    }
    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .btn-save {
        padding: 0.6rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
</style>
@endsection