<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Créer un compte</title>

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

            <h1 class="h3 mb-2">
                Créer un compte
            </h1>

            <p class="text-secondary mb-4">
                Inscrivez-vous pour gérer vos boutiques.
            </p>

            <form
                method="POST"
                action="{{ route('register.store') }}"
                enctype="multipart/form-data"
            >

                @csrf

                <div class="mb-3">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Nom
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label"
                    >
                        Adresse e-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        required
                    >

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label
                        for="password"
                        class="form-label"
                    >
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                    >

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label
                        for="password_confirmation"
                        class="form-label"
                    >
                        Confirmer le mot de passe
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >

                </div>

               <div class="mb-4">

    <label
        for="registre_commerce"
        class="form-label"
    >
        Registre de commerce
    </label>

    <input
        type="file"
        id="registre_commerce"
        name="registre_commerce"
        accept=".pdf,.jpg,.jpeg,.png"
        class="form-control @error('registre_commerce') is-invalid @enderror"
        required
    >

    <div class="form-text">
        PDF, JPG ou PNG — maximum 5 Mo.
    </div>

    @error('registre_commerce')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>

                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    Créer mon compte
                </button>

            </form>

            <div class="text-center mt-3">

                <a href="{{ route('login') }}">
                    J'ai déjà un compte
                </a>

            </div>

        </div>

    </div>

</main>

</body>

</html>