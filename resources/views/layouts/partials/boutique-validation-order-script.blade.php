@if (
    auth()->check()
    && auth()->user()->role === 'client'
    && Route::has('boutique-validation.statuses')
)
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const inputs = document.querySelectorAll('[name="id_boutique"]');

    if (! inputs.length) {
        return;
    }

    try {
        const response = await fetch(
            @json(route('boutique-validation.statuses')),
            {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }
        );

        if (! response.ok) {
            return;
        }

        const statuses = await response.json();

        inputs.forEach(function (input) {
            if (input.tagName.toLowerCase() === 'select') {
                Array.from(input.options).forEach(function (option) {
                    const info = statuses[String(option.value)];

                    if (! info || info.autorisee) {
                        return;
                    }

                    option.disabled = true;

                    const suffix =
                        info.statut === 'refusee'
                            ? ' — justificatif refusé'
                            : ' — en attente de validation';

                    if (! option.textContent.includes(suffix)) {
                        option.textContent += suffix;
                    }
                });

                return;
            }

            const info = statuses[String(input.value)];

            if (! info || info.autorisee) {
                return;
            }

            input.disabled = true;

            input.setAttribute(
                'title',
                info.statut === 'refusee'
                    ? 'Justificatif refusé'
                    : 'En attente de validation'
            );
        });
    } catch (error) {
        // Le garde-fou serveur reste actif même sans JavaScript.
    }
});
</script>
@endif
