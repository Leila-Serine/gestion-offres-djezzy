<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mes commandes</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h2">Mes commandes</h1>

            <p class="text-secondary mb-0">
                Consultez les commandes passées pour vos boutiques.
            </p>
        </div>

        <a
            href="{{ route('commandes.create') }}"
            class="btn btn-primary"
        >
            Nouvelle commande
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Identifiant</th>
                            <th>Boutique</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($commandes as $commande)

                            <tr>

                                <td>
                                    {{ $commande->id_commande }}
                                </td>

                                <td>
                                    {{ $commande->boutique->nom_boutique }}
                                </td>

                                <td>
                                    {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                </td>

                                <td>
                                    {{ $commande->created_at?->timezone('Africa/Algiers')->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    <a
                                        href="{{ route('commandes.show', $commande->id_commande) }}"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        Voir
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="text-center text-secondary py-4"
                                >
                                    Aucune commande enregistrée.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</main>

</body>
</html>