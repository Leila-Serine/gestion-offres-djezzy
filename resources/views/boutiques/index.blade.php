<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Mes boutiques</title>

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
                    Mes boutiques
                </h1>

                <p class="text-secondary mb-0">
                    Consultez les boutiques associées à votre compte.
                </p>
            </div>

            <a
                href="{{ route('boutiques.create') }}"
                class="btn btn-primary"
            >
                Ajouter une boutique
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
                                <th>Nom</th>
                                <th>Wilaya</th>
                                <th>Adresse</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($boutiques as $boutique)

                                <tr>

                                    <td>
                                        {{ $boutique->id_boutique }}
                                    </td>

                                    <td>
                                        {{ $boutique->nom_boutique }}
                                    </td>

                                    <td>
                                        {{ $boutique->wilaya }}
                                    </td>

                                    <td>
                                        {{ $boutique->adresse }}
                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <a
                                                href="{{ route('boutiques.show', $boutique->id_boutique) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                Voir
                                            </a>

                                            <a
                                                href="{{ route('boutiques.edit', $boutique->id_boutique) }}"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Modifier
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('boutiques.destroy', $boutique->id_boutique) }}"
                                                onsubmit="return confirm('Voulez-vous vraiment supprimer cette boutique ?');"
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
                                    <td
                                        colspan="5"
                                        class="text-center text-secondary py-4"
                                    >
                                        Vous n'avez aucune boutique enregistrée.
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