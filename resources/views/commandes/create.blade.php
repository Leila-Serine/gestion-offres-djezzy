<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nouvelle commande</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div class="mb-4">
        <h1 class="h2">Nouvelle commande</h1>

        <p class="text-secondary">
            Choisissez une boutique et les offres à commander.
        </p>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('commandes.store') }}"
            >
                @csrf

                <div class="mb-4">

                    <label
                        for="id_boutique"
                        class="form-label"
                    >
                        Boutique
                    </label>

                    <select
                        id="id_boutique"
                        name="id_boutique"
                        class="form-select @error('id_boutique') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Sélectionner une boutique
                        </option>

                        @foreach ($boutiques as $boutique)

                            <option
                                value="{{ $boutique->id_boutique }}"
                                @selected(old('id_boutique') == $boutique->id_boutique)
                            >
                                {{ $boutique->nom_boutique }}
                                -
                                {{ $boutique->wilaya }}
                            </option>

                        @endforeach

                    </select>

                    @error('id_boutique')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <h2 class="h5 mb-3">
                    Offres
                </h2>

                @forelse ($offres as $index => $offre)

                    <div class="border rounded p-3 mb-3">

                        <div class="row align-items-center">

                            <div class="col-md-6">

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="offre_{{ $offre->id_offre }}"
                                        onchange="toggleQuantite(
                                            {{ $offre->id_offre }}
                                        )"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="offre_{{ $offre->id_offre }}"
                                    >
                                        <strong>
                                            {{ $offre->nom_offre }}
                                        </strong>

                                        <br>

                                        <span class="text-secondary">
                                            {{ $offre->categorie }}
                                        </span>
                                    </label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <input
                                    type="hidden"
                                    name="lignes[{{ $index }}][id_offre]"
                                    value="{{ $offre->id_offre }}"
                                    disabled
                                    id="offre_hidden_{{ $offre->id_offre }}"
                                >

                                <label
                                    for="quantite_{{ $offre->id_offre }}"
                                    class="form-label"
                                >
                                    Quantité
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    name="lignes[{{ $index }}][quantite]"
                                    id="quantite_{{ $offre->id_offre }}"
                                    class="form-control"
                                    disabled
                                >

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="alert alert-warning">
                        Aucune offre disponible.
                    </div>

                @endforelse

                @error('lignes')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-flex gap-2 mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Passer la commande
                    </button>

                    <a
                        href="{{ route('commandes.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </div>

</main>

<script>
    function toggleQuantite(idOffre) {
        const checkbox =
            document.getElementById('offre_' + idOffre);

        const quantite =
            document.getElementById('quantite_' + idOffre);

        const hidden =
            document.getElementById('offre_hidden_' + idOffre);

        quantite.disabled = !checkbox.checked;
        hidden.disabled = !checkbox.checked;

        if (!checkbox.checked) {
            quantite.value = '';
        }
    }
</script>

</body>
</html>