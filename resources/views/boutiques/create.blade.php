<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ajouter une boutique</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div class="mb-4">
        <h1 class="h2">Ajouter une boutique</h1>

        <p class="text-secondary">
            Enregistrez une nouvelle boutique associée à votre compte.
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form
                method="POST"
                action="{{ route('boutiques.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mb-3">
                    <label for="nom_boutique" class="form-label">
                        Nom de la boutique
                    </label>

                    <input
                        type="text"
                        id="nom_boutique"
                        name="nom_boutique"
                        class="form-control @error('nom_boutique') is-invalid @enderror"
                        value="{{ old('nom_boutique') }}"
                        required
                    >

                    @error('nom_boutique')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="wilaya" class="form-label">
                        Wilaya
                    </label>

                    <input
                        type="text"
                        id="wilaya"
                        name="wilaya"
                        class="form-control @error('wilaya') is-invalid @enderror"
                        value="{{ old('wilaya') }}"
                        required
                    >

                    @error('wilaya')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">
                        Adresse
                    </label>

                    <input
                        type="text"
                        id="adresse"
                        name="adresse"
                        class="form-control @error('adresse') is-invalid @enderror"
                        value="{{ old('adresse') }}"
                        required
                    >

                    @error('adresse')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="justificatif_local" class="form-label">
                        Justificatif du local
                    </label>

                    <input
                        type="file"
                        id="justificatif_local"
                        name="justificatif_local"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="form-control @error('justificatif_local') is-invalid @enderror"
                        required
                    >

                    <div class="form-text">
                        Par exemple : contrat de location, titre de propriété ou justificatif équivalent.
                        Formats acceptés : PDF, JPG, JPEG ou PNG — maximum 5 Mo.
                    </div>

                    @error('justificatif_local')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Enregistrer
                    </button>

                    <a
                        href="{{ route('boutiques.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>
    </div>

</main>

</body>
</html>