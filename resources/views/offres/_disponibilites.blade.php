@php
    $zonesDisponibles = config('offres.zones', []);

    $disponibilitesForm = old(
        'disponibilites',
        isset($offre)
            ? $offre->disponibilites->map(fn ($disponibilite) => [
                'zone' => $disponibilite->zone,
                'quantite_disponible' => $disponibilite->quantite_disponible,
            ])->values()->all()
            : []
    );

    if (count($disponibilitesForm) === 0) {
        $disponibilitesForm = [
            ['zone' => 'Alger', 'quantite_disponible' => 0],
        ];
    }
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Stock régional</h2>
                <p class="text-muted small mb-0">
                    Saisissez la quantité disponible pour chaque zone.
                </p>
            </div>

            <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                id="add-stock-zone"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Ajouter une zone
            </button>
        </div>

        <div id="stock-zones-container">
            @foreach ($disponibilitesForm as $index => $disponibilite)
                <div class="row g-2 align-items-end stock-zone-row mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Zone / wilaya</label>

                        <select
                            class="form-select"
                            name="disponibilites[{{ $index }}][zone]"
                        >
                            <option value="">Choisir...</option>

                            @foreach ($zonesDisponibles as $zone)
                                <option
                                    value="{{ $zone }}"
                                    @selected(($disponibilite['zone'] ?? '') === $zone)
                                >
                                    {{ $zone }}
                                </option>
                            @endforeach

                            @if (
                                !empty($disponibilite['zone'])
                                && !in_array($disponibilite['zone'], $zonesDisponibles, true)
                            )
                                <option value="{{ $disponibilite['zone'] }}" selected>
                                    {{ $disponibilite['zone'] }}
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Quantité en stock</label>

                        <input
                            type="number"
                            min="0"
                            step="1"
                            class="form-control"
                            name="disponibilites[{{ $index }}][quantite_disponible]"
                            value="{{ $disponibilite['quantite_disponible'] ?? 0 }}"
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button
                            type="button"
                            class="btn btn-outline-danger remove-stock-zone"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @error('disponibilites')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('stock-zones-container');
                const addButton = document.getElementById('add-stock-zone');

                if (!container || !addButton) {
                    return;
                }

                const zones = @json($zonesDisponibles);

                function refreshIndexes() {
                    container.querySelectorAll('.stock-zone-row').forEach((row, index) => {
                        row.querySelectorAll('[name]').forEach((field) => {
                            field.name = field.name.replace(
                                /disponibilites\[\d+\]/,
                                `disponibilites[${index}]`
                            );
                        });
                    });
                }

                container.addEventListener('click', (event) => {
                    const button = event.target.closest('.remove-stock-zone');

                    if (!button) {
                        return;
                    }

                    button.closest('.stock-zone-row').remove();
                    refreshIndexes();
                });

                addButton.addEventListener('click', () => {
                    const index = container.querySelectorAll('.stock-zone-row').length;

                    const options = zones
                        .map((zone) => `<option value="${zone}">${zone}</option>`)
                        .join('');

                    container.insertAdjacentHTML('beforeend', `
                        <div class="row g-2 align-items-end stock-zone-row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Zone / wilaya</label>
                                <select
                                    class="form-select"
                                    name="disponibilites[${index}][zone]"
                                >
                                    <option value="">Choisir...</option>
                                    ${options}
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quantité en stock</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="form-control"
                                    name="disponibilites[${index}][quantite_disponible]"
                                    value="0"
                                >
                            </div>

                            <div class="col-md-2 d-grid">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger remove-stock-zone"
                                >
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    `);
                });
            });
        </script>
    @endpush
@endonce
