<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Modifier une boutique</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div class="mb-4">
        <h1 class="h2">Modifier la boutique</h1>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('boutiques.update', $boutique->id_boutique) }}"
            >
                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label for="nom_boutique" class="form-label">
                        Nom de la boutique
                    </label>

                    <input
                        type="text"
                        id="nom_boutique"
                        name="nom_boutique"
                        class="form-control @error('nom_boutique') is-invalid @enderror"
                        value="{{ old('nom_boutique', $boutique->nom_boutique) }}"
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
                        value="{{ old('wilaya', $boutique->wilaya) }}"
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
                        value="{{ old('adresse', $boutique->adresse) }}"
                        required
                    >

                    @error('adresse')
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
                        Enregistrer les modifications
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