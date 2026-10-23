@php
    $definitionsCaracteristiques = config('offres.caracteristiques', []);

    $caracteristiquesForm = old(
        'caracteristiques',
        isset($offre)
            ? $offre->caracteristiques->map(function ($caracteristique) use ($definitionsCaracteristiques) {
                $code = $caracteristique->code;

                return [
                    'code' => $code,
                    'valeur_numerique' => $caracteristique->valeur_numerique
                        ?? $caracteristique->valeur,
                    'unite' => $caracteristique->unite
                        ?? ($definitionsCaracteristiques[$code]['unite_defaut'] ?? ''),
                ];
            })->values()->all()
            : []
    );

    if (count($caracteristiquesForm) === 0) {
        $caracteristiquesForm = [
            [
                'code' => 'internet',
                'valeur_numerique' => '',
                'unite' => $definitionsCaracteristiques['internet']['unite_defaut'] ?? 'Go',
            ],
            [
                'code' => 'duree',
                'valeur_numerique' => '',
                'unite' => $definitionsCaracteristiques['duree']['unite_defaut'] ?? 'Jours',
            ],
        ];
    }
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Caractéristiques</h2>
                <p class="text-muted small mb-0">
                    Le type et l'unité sont sélectionnés dans des listes normalisées.
                </p>
            </div>

            <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                id="add-characteristic"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Ajouter
            </button>
        </div>

        <div id="characteristics-container">
            @foreach ($caracteristiquesForm as $index => $caracteristique)
                @php
                    $currentCode = $caracteristique['code'] ?? '';
                    $availableUnits = $definitionsCaracteristiques[$currentCode]['unites'] ?? [];
                    $currentUnit = $caracteristique['unite'] ?? '';
                @endphp

                <div class="row g-2 align-items-end characteristic-row mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Type</label>

                        <select
                            class="form-select characteristic-code"
                            name="caracteristiques[{{ $index }}][code]"
                        >
                            <option value="">Choisir...</option>

                            @foreach ($definitionsCaracteristiques as $code => $definition)
                                <option
                                    value="{{ $code }}"
                                    @selected($currentCode === $code)
                                >
                                    {{ $definition['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Valeur</label>

                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            name="caracteristiques[{{ $index }}][valeur_numerique]"
                            value="{{ $caracteristique['valeur_numerique'] ?? '' }}"
                            placeholder="Ex. 40"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Unité</label>

                        <select
                            class="form-select characteristic-unit"
                            name="caracteristiques[{{ $index }}][unite]"
                        >
                            @foreach ($availableUnits as $unit)
                                <option
                                    value="{{ $unit }}"
                                    @selected($currentUnit === $unit)
                                >
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button
                            type="button"
                            class="btn btn-outline-danger remove-characteristic"
                        >
                            Supprimer
                        </button>
                    </div>

                    @error("caracteristiques.$index.unite")
                        <div class="col-12">
                            <div class="text-danger small">{{ $message }}</div>
                        </div>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('characteristics-container');
                const addButton = document.getElementById('add-characteristic');

                if (!container || !addButton) {
                    return;
                }

                const definitions = @json($definitionsCaracteristiques);

                function unitOptions(code, selected = null) {
                    const units = definitions[code]?.unites ?? [];

                    return units.map((unit) => {
                        const isSelected = unit === selected ? ' selected' : '';
                        return `<option value="${unit}"${isSelected}>${unit}</option>`;
                    }).join('');
                }

                function refreshIndexes() {
                    container.querySelectorAll('.characteristic-row').forEach((row, index) => {
                        row.querySelectorAll('[name]').forEach((field) => {
                            field.name = field.name.replace(
                                /caracteristiques\[\d+\]/,
                                `caracteristiques[${index}]`
                            );
                        });
                    });
                }

                function refreshUnits(row) {
                    const codeSelect = row.querySelector('.characteristic-code');
                    const unitSelect = row.querySelector('.characteristic-unit');

                    if (!codeSelect || !unitSelect) {
                        return;
                    }

                    const code = codeSelect.value;
                    const defaultUnit = definitions[code]?.unite_defaut ?? null;

                    unitSelect.innerHTML = unitOptions(code, defaultUnit);
                }

                container.addEventListener('change', (event) => {
                    if (event.target.classList.contains('characteristic-code')) {
                        refreshUnits(event.target.closest('.characteristic-row'));
                    }
                });

                container.addEventListener('click', (event) => {
                    const button = event.target.closest('.remove-characteristic');

                    if (!button) {
                        return;
                    }

                    button.closest('.characteristic-row').remove();
                    refreshIndexes();
                });

                addButton.addEventListener('click', () => {
                    const index = container.querySelectorAll('.characteristic-row').length;

                    const typeOptions = Object.entries(definitions)
                        .map(([code, definition]) =>
                            `<option value="${code}">${definition.label}</option>`
                        )
                        .join('');

                    container.insertAdjacentHTML('beforeend', `
                        <div class="row g-2 align-items-end characteristic-row mb-2">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type</label>
                                <select
                                    class="form-select characteristic-code"
                                    name="caracteristiques[${index}][code]"
                                >
                                    <option value="">Choisir...</option>
                                    ${typeOptions}
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Valeur</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="form-control"
                                    name="caracteristiques[${index}][valeur_numerique]"
                                    placeholder="Ex. 40"
                                >
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Unité</label>
                                <select
                                    class="form-select characteristic-unit"
                                    name="caracteristiques[${index}][unite]"
                                >
                                </select>
                            </div>

                            <div class="col-md-2 d-grid">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger remove-characteristic"
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
