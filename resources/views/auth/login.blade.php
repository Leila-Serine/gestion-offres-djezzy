<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="card shadow-sm" style="max-width: 420px; width: 100%;">

        <div class="card-body p-4">

            <h1 class="h4 text-center mb-4">
                Connexion
            </h1>

            <!-- Affichage des erreurs de validation ou de connexion -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">

                @csrf

                <!-- Adresse email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Adresse e-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <!-- Mot de passe -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <!-- Se souvenir de moi -->
                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="remember"
                        name="remember"
                    >

                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                    </label>
                </div>

                <!-- Bouton de connexion -->
                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    Se connecter
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>