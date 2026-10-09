@if (
    isset($boutique)
    && auth()->check()
    && auth()->user()->role === 'client'
    && (int) $boutique->id_client === (int) auth()->user()->id_user
    && $boutique->statut_validation_justificatif !== null
)

<div class="app-card mt-4">

    <div class="section-header">

        <div>
            <h2 class="section-title">
                Validation du justificatif
            </h2>

            <p class="section-subtitle">
                Statut administratif de cette boutique
            </p>
        </div>

        @if ($boutique->statut_validation_justificatif === 'validee')
            <span class="status-pill status-done">
                Validée
            </span>
        @elseif ($boutique->statut_validation_justificatif === 'refusee')
            <span class="status-pill status-refused">
                Refusée
            </span>
        @else
            <span class="status-pill status-wait">
                En attente
            </span>
        @endif

    </div>


    <div class="section-body">

        @if ($boutique->statut_validation_justificatif === 'validee')

            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle me-2"></i>
                Le justificatif de cette boutique a été validé.
                Elle peut être utilisée pour passer une nouvelle commande.
            </div>

        @elseif ($boutique->statut_validation_justificatif === 'refusee')

            <div class="alert alert-danger">
                <strong>Justificatif non validé</strong>

                <div class="mt-2">
                    <strong>Motif :</strong><br>
                    {{ $boutique->motif_refus_justificatif }}
                </div>
            </div>

            @if (Route::has('boutiques.validation.document'))
                <a
                    href="{{ route('boutiques.validation.document', $boutique) }}"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-soft w-100 mb-3"
                >
                    <i class="bi bi-eye me-2"></i>
                    Consulter le document refusé
                </a>
            @endif

            <form
                method="POST"
                action="{{ route('boutiques.validation.replace', $boutique) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <label
                    for="justificatif_local_validation"
                    class="form-label"
                >
                    Remplacer le justificatif
                </label>

                <input
                    id="justificatif_local_validation"
                    type="file"
                    name="justificatif_local"
                    class="form-control @error('justificatif_local') is-invalid @enderror"
                    accept=".pdf,.jpg,.jpeg,.png"
                    required
                >

                @error('justificatif_local')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                <div class="form-text">
                    PDF, JPG, JPEG ou PNG — maximum 5 Mo.
                </div>

                <button
                    type="submit"
                    class="btn btn-djezzy mt-3"
                >
                    <i class="bi bi-upload me-2"></i>
                    Envoyer le nouveau justificatif
                </button>
            </form>

        @else

            <div class="alert alert-warning mb-0">
                <i class="bi bi-hourglass-split me-2"></i>
                Le justificatif de cette boutique est en attente de validation
                par un commercial. Cette boutique ne peut pas encore être
                utilisée pour une nouvelle commande.
            </div>

        @endif

    </div>

</div>

@endif
