<section>
    <header class="mb-4">
        <p class="text-muted small">Veillez à ce que votre compte utilise un mot de passe long et aléatoire pour rester en sécurité.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label">Mot de passe actuel</label>
            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" />
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-warning text-dark fw-bold btn-save shadow-sm">
                Mettre à jour le mot de passe
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success small fw-bold">Mot de passe mis à jour.</span>
            @endif
        </div>
    </form>
</section>