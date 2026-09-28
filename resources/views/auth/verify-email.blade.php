<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Vérification de l'adresse e-mail</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container py-5">

    <div
        class="card shadow-sm mx-auto"
        style="max-width: 600px;"
    >

        <div class="card-body p-4">

            <h1 class="h3 mb-3">
                Vérifiez votre adresse e-mail
            </h1>

            <p class="text-secondary">
                Un lien de validation a été envoyé à :
            </p>

            <p>
                <strong>
                    {{ auth()->user()->email }}
                </strong>
            </p>

            <p class="text-secondary">
                Cliquez sur le lien reçu pour activer votre compte.
            </p>

            @if (session('status') === 'verification-link-sent')

                <div class="alert alert-success">
                    Un nouveau lien de vérification a été envoyé.
                </div>

            @endif

            <form
                method="POST"
                action="{{ route('verification.send') }}"
                class="mb-3"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Renvoyer le lien
                </button>

            </form>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-outline-secondary"
                >
                    Se déconnecter
                </button>

            </form>

        </div>

    </div>

</main>

</body>
</html>