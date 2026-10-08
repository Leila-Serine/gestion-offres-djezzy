@php
    $edition = isset($boutique);
@endphp

<div class="row g-3">

    <div class="col-md-6">
        <label
            for="nom_boutique"
            class="form-label"
        >
            Nom de la boutique
        </label>

        <input
            id="nom_boutique"
            type="text"
            name="nom_boutique"
            class="form-control @error('nom_boutique') is-invalid @enderror"
            value="{{ old('nom_boutique', $boutique->nom_boutique ?? '') }}"
            maxlength="100"
            required
        >

        @error('nom_boutique')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="col-md-6">
        <label
            for="wilaya"
            class="form-label"
        >
            Wilaya
        </label>

        <select
            id="wilaya"
            name="wilaya"
            class="form-select @error('wilaya') is-invalid @enderror"
            required
        >
            <option value="">
                Sélectionner une wilaya
            </option>

            @foreach ($wilayas as $wilaya)
                <option
                    value="{{ $wilaya }}"
                    {{ old(
                        'wilaya',
                        $boutique->wilaya ?? ''
                    ) === $wilaya ? 'selected' : '' }}
                >
                    {{ $wilaya }}
                </option>
            @endforeach
        </select>

        @error('wilaya')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        <div class="form-text">
            Cette wilaya est utilisée pour déterminer
            la disponibilité régionale des offres.
        </div>
    </div>


    <div class="col-12">
        <label
            for="adresse"
            class="form-label"
        >
            Adresse
        </label>

        <input
            id="adresse"
            type="text"
            name="adresse"
            class="form-control @error('adresse') is-invalid @enderror"
            value="{{ old('adresse', $boutique->adresse ?? '') }}"
            maxlength="255"
            required
        >

        @error('adresse')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="col-12">
        <label
            for="justificatif_local"
            class="form-label"
        >
            Justificatif du local
        </label>

        @if (
            $edition
            && isset($justificatif)
            && $justificatif
        )
            <div
                class="d-flex align-items-center justify-content-between gap-3
                       border rounded-3 p-3 mb-3 bg-light"
            >
                <div>
                    <div class="fw-semibold">
                        Un justificatif est déjà enregistré
                    </div>

                    <div class="small text-secondary">
                        Laissez le champ vide pour conserver ce document.
                    </div>
                </div>

                <a
                    href="{{ route(
                        'boutiques.document',
                        [$boutique, $justificatif]
                    ) }}"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-soft btn-sm"
                >
                    <i class="bi bi-eye me-1"></i>
                    Consulter
                </a>
            </div>
        @endif

        <input
            id="justificatif_local"
            type="file"
            name="justificatif_local"
            class="form-control @error('justificatif_local') is-invalid @enderror"
            accept=".pdf,.jpg,.jpeg,.png"
            {{ $edition ? '' : 'required' }}
        >

        @error('justificatif_local')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        <div class="form-text">
            Par exemple : contrat de location,
            titre de propriété ou justificatif équivalent.
            Formats acceptés : PDF, JPG, JPEG ou PNG —
            maximum 5 Mo.
        </div>
    </div>

</div>
