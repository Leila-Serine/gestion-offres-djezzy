<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Détail de l'offre</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <main class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="h2">
                    Détail de l'offre
                </h1>

                <p class="text-secondary mb-0">
                    Consultez les informations de l'offre sélectionnée.
                </p>
            </div>

            <a
                href="{{ route('offres.index') }}"
                class="btn btn-outline-secondary"
            >
                Retour
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <dl class="row mb-0">

                    <dt class="col-sm-4">
                        Identifiant
                    </dt>

                    <dd class="col-sm-8">
                        {{ $offre->id_offre }}
                    </dd>

                    <dt class="col-sm-4">
                        Nom de l'offre
                    </dt>

                    <dd class="col-sm-8">
                        {{ $offre->nom_offre }}
                    </dd>

                    <dt class="col-sm-4">
                        Catégorie
                    </dt>

                    <dd class="col-sm-8">
                        {{ $offre->categorie }}
                    </dd>

                    <dt class="col-sm-4">
                        Date de création
                    </dt>

                    <dd class="col-sm-8">
                        {{ $offre->created_at?->timezone('Africa/Algiers')->format('d/m/Y H:i') }}
                    </dd>

                </dl>

            </div>

            @if (auth()->user()->role === 'commercial')

                <div class="card-footer bg-white">

                    <a
                        href="{{ route('offres.edit', $offre->id_offre) }}"
                        class="btn btn-primary"
                    >
                        Modifier
                    </a>

                </div>

            @endif

        </div>

    </main>

</body>
</html>