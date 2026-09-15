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
        <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2">Gestion des offres</h1>
        @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

        <p class="text-secondary mb-0">
            Consultez et gérez les offres enregistrées.
        </p>
    </div>

    <a
        href="{{ route('offres.create') }}"
        class="btn btn-primary"
    >
        Ajouter une offre
    </a>
</div>

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
                                    <td>{{ $offre->id_offre }}</td>
                                    <td>{{ $offre->nom_offre }}</td>
                                    <td>{{ $offre->categorie }}</td>

                                    <td>
                                        {{ $offre->created_at?->timezone('Africa/Algiers')->format('d/m/Y H:i') }}
                                    </td>
                                   <td>
    <div class="d-flex gap-2">
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
    </div>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
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