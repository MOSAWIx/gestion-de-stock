<section class="space-y-6">
    <header>
        <p class="text-muted small">
            Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
        </p>
    </header>

    <button type="button" 
            class="btn btn-danger btn-save shadow-sm" 
            data-bs-toggle="modal" 
            data-bs-target="#confirmUserDeletion">
        Supprimer le compte
    </button>

    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-danger">Êtes-vous sûr ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted">Veuillez saisir votre mot de passe pour confirmer la suppression définitive.</p>
                    <div class="mt-3">
                        <label for="password_del" class="form-label">Mot de passe</label>
                        <input id="password_del" name="password" type="password" class="form-control" placeholder="Mot de passe" required />
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</section>