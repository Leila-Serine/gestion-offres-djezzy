@php
    $internetUnits = config('offres.caracteristiques.internet.unites', []);
    $dureeUnits = config('offres.caracteristiques.duree.unites', []);
@endphp

<form method="GET" action="{{ route('offres.index') }}" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">Filtrer les offres</h2>
                <p class="text-muted small mb-0">
                    Choisissez la valeur et l'unité à comparer.
                </p>
            </div>

            <a href="{{ route('offres.index') }}" class="btn btn-outline-secondary btn-sm">
                Réinitialiser
            </a>
        </div>

        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Catégorie</label>

                <select name="categorie" class="form-select">
                    <option value="">Toutes</option>

                    @foreach ($categories as $categorie)
                        <option
                            value="{{ $categorie }}"
                            @selected(request('categorie') === $categorie)
                        >
                            {{ $categorie }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label fw-semibold">Prix min.</label>

                <div class="input-group">
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="prix_min"
                        class="form-control"
                        value="{{ request('prix_min') }}"
                    >
                    <span class="input-group-text">DA</span>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label fw-semibold">Prix max.</label>

                <div class="input-group">
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="prix_max"
                        class="form-control"
                        value="{{ request('prix_max') }}"
                    >
                    <span class="input-group-text">DA</span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Internet min.</label>

                <div class="input-group">
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="internet_min"
                        class="form-control"
                        value="{{ request('internet_min') }}"
                    >

                    <select
                        name="internet_unite"
                        class="form-select"
                        style="max-width: 95px"
                    >
                        @foreach ($internetUnits as $unit)
                            <option
                                value="{{ $unit }}"
                                @selected(request('internet_unite', 'Go') === $unit)
                            >
                                {{ $unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Durée min.</label>

                <div class="input-group">
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="duree_min"
                        class="form-control"
                        value="{{ request('duree_min') }}"
                    >

                    <select
                        name="duree_unite"
                        class="form-select"
                        style="max-width: 115px"
                    >
                        @foreach ($dureeUnits as $unit)
                            <option
                                value="{{ $unit }}"
                                @selected(request('duree_unite', 'Jours') === $unit)
                            >
                                {{ $unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Disponible dans la zone</label>

                <select name="zone" class="form-select">
                    <option value="">Toutes les zones</option>

                    @foreach (config('offres.zones', []) as $zone)
                        <option
                            value="{{ $zone }}"
                            @selected(request('zone') === $zone)
                        >
                            {{ $zone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Tri</label>

                <select name="tri" class="form-select">
                    <option value="">Plus récentes</option>
                    <option value="prix_asc" @selected(request('tri') === 'prix_asc')>
                        Prix croissant
                    </option>
                    <option value="prix_desc" @selected(request('tri') === 'prix_desc')>
                        Prix décroissant
                    </option>
                    <option value="nom_asc" @selected(request('tri') === 'nom_asc')>
                        Nom A → Z
                    </option>
                    <option value="nom_desc" @selected(request('tri') === 'nom_desc')>
                        Nom Z → A
                    </option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6 d-grid align-self-end">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-funnel me-1"></i>
                    Filtrer
                </button>
            </div>
        </div>
    </div>
</form>
