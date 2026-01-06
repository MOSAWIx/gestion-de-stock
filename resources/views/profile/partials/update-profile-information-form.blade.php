<section>
    <header class="mb-4">
        <p class="text-muted small">Mettez à jour les informations de profil et l'adresse e-mail de votre compte.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nom complet</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if($errors->has('name')) <div class="text-danger small mt-1">{{ $errors->first('name') }}</div> @endif
        </div>

        <div class="mb-4">
            <label for="email" class="form-label">Adresse E-mail</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if($errors->has('email')) <div class="text-danger small mt-1">{{ $errors->first('email') }}</div> @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary btn-save shadow-sm">
                Enregistrer les modifications
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small fw-bold">
                    <i class="bi bi-check-lg"></i> Modifié avec succès.
                </span>
            @endif
        </div>
    </form>
</section>