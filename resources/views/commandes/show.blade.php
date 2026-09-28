<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Détail commande</title>

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
                Commande #{{ $commande->id_commande }}
            </h1>

            <p class="text-secondary mb-0">
                Détail de la commande.
            </p>
        </div>

        <a
            href="{{ route('commandes.index') }}"
            class="btn btn-outline-secondary"
        >
            Retour
        </a>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <dl class="row mb-0">

                <dt class="col-sm-4">
                    Boutique
                </dt>

                <dd class="col-sm-8">
                    {{ $commande->boutique->nom_boutique }}
                </dd>

                <dt class="col-sm-4">
                    Wilaya
                </dt>

                <dd class="col-sm-8">
                    {{ $commande->boutique->wilaya }}
                </dd>

                <dt class="col-sm-4">
                    Statut
                </dt>

                <dd class="col-sm-8">
                    {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                </dd>

                <dt class="col-sm-4">
                    Date
                </dt>

                <dd class="col-sm-8">
                    {{ $commande->created_at?->timezone('Africa/Algiers')->format('d/m/Y H:i') }}
                </dd>

            </dl>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-white">
            <strong>Offres commandées</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>Offre</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($commande->lignes as $ligne)

                            <tr>

                                <td>
                                    {{ $ligne->offre->nom_offre }}
                                </td>

                                <td>
                                    {{ $ligne->offre->categorie }}
                                </td>

                                <td>
                                    {{ $ligne->quantite }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</main>

</body>
</html>