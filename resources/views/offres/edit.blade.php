<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Modifier une offre</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">
    <main class="container py-5">
        <div class="mb-4">
            <h1 class="h2">Modifier une offre</h1>

            <p class="text-secondary">
                Modifiez les informations de l'offre.
            </p>
        </div>
@if ($errors->any())
    <div class="alert alert-danger">
        <p class="mb-2">
            Veuillez corriger les erreurs suivantes :
        </p>

        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <div class="card shadow-sm">
            <div class="card-body">
                <form
                    action="{{ route('offres.update', $offre->id_offre) }}"
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nom_offre" class="form-label">
                            Nom de l'offre
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nom_offre"
                            name="nom_offre"
                            value="{{ old('nom_offre', $offre->nom_offre) }}"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="categorie" class="form-label">
                            Catégorie
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="categorie"
                            name="categorie"
                            value="{{ old('categorie', $offre->categorie) }}"
                            maxlength="20"
                            required
                        >
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>

                        <a
                            href="{{ route('offres.index') }}"
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