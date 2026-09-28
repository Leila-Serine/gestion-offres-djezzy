<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Gestion des offres</title>

    <!-- Bootstrap 5 fournit les styles visuels de la page. -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <main class="container py-5">

     <div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h2">Gestion des offres</h1>

        <p class="text-secondary mb-0">
            Consultez les offres enregistrées.
        </p>
    </div>

    <div class="d-flex align-items-center gap-3">

        <!-- Informations sur l'utilisateur connecté -->
        <div class="text-end">
            <div class="fw-semibold">
                {{ auth()->user()->name }}
            </div>

            <small class="text-secondary">
                {{ ucfirst(auth()->user()->role) }}
            </small>
        </div>

        <!-- Seul le commercial peut ajouter une offre -->
        @if (auth()->user()->role === 'commercial')
            <a
                href="{{ route('offres.create') }}"
                class="btn btn-primary"
            >
                Ajouter une offre
            </a>
        @endif

        <!-- Déconnexion -->
        <form
            method="POST"
            action="{{ route('logout') }}"
            class="mb-0"
        >
            @csrf

            <button
                type="submit"
                class="btn btn-outline-secondary"
            >
                Déconnexion
            </button>
        </form>

    </div>

</div>

        <!-- Message affiché après une opération réussie. -->
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
                                <th>Nom de l'offre</th>
                                <th>Catégorie</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($offres as $offre)

                                <tr>

                                    <td>
                                        {{ $offre->id_offre }}
                                    </td>

                                    <td>
                                        {{ $offre->nom_offre }}
                                    </td>

                                    <td>
                                        {{ $offre->categorie }}
                                    </td>

                                    <td>
                                        {{ $offre->created_at?->timezone('Africa/Algiers')->format('d/m/Y H:i') }}
                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <!--
                                                Client et commercial peuvent
                                                consulter une offre.
                                            -->
                                            <a
                                                href="{{ route('offres.show', $offre->id_offre) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                Voir
                                            </a>

                                            <!--
                                                Les actions de gestion sont
                                                réservées au commercial.
                                            -->
                                            @if (auth()->user()->role === 'commercial')

                                                <a
                                                    href="{{ route('offres.edit', $offre->id_offre) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    Modifier
                                                </a>

                                                <form
                                                    action="{{ route('offres.destroy', $offre->id_offre) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        Supprimer
                                                    </button>

                                                </form>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="text-center text-secondary py-4"
                                    >
                                        Aucune offre n'est enregistrée pour le moment.
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