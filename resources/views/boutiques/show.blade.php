<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Détail boutique</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h2">Détail de la boutique</h1>

            <p class="text-secondary mb-0">
                Informations de la boutique sélectionnée.
            </p>
        </div>

        <a
            href="{{ route('boutiques.index') }}"
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
                    {{ $boutique->id_boutique }}
                </dd>

                <dt class="col-sm-4">
                    Nom
                </dt>

                <dd class="col-sm-8">
                    {{ $boutique->nom_boutique }}
                </dd>

                <dt class="col-sm-4">
                    Wilaya
                </dt>

                <dd class="col-sm-8">
                    {{ $boutique->wilaya }}
                </dd>

                <dt class="col-sm-4">
                    Adresse
                </dt>

                <dd class="col-sm-8">
                    {{ $boutique->adresse }}
                </dd>

            </dl>

        </div>

        <div class="card-footer bg-white">

            <a
                href="{{ route('boutiques.edit', $boutique->id_boutique) }}"
                class="btn btn-primary"
            >
                Modifier
            </a>

        </div>

    </div>

</main>

</body>
</html>