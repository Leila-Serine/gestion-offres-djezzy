<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Validation du compte · Djezzy
    </title>

    <link
        rel="icon"
        type="image/svg+xml"
        href="{{ asset('images/djezzy-logo.svg') }}"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --djezzy-red: #e30613;
            --djezzy-dark: #1c2027;
            --muted: #737b87;
            --border: #e7e9ee;
        }

        body {
            margin: 0;
            background: #f5f6f8;
            color: #20242b;
            font-family:
                Inter,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        .validation-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns:
                minmax(300px, 42%)
                1fr;
            background: #fff;
        }

        .validation-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 38px clamp(28px, 4vw, 72px);
            background:
                linear-gradient(
                    145deg,
                    #262b33,
                    #171b21
                );
            color: #fff;
        }

        .validation-panel::after {
            position: absolute;
            right: -180px;
            bottom: -220px;
            width: 550px;
            height: 550px;
            border: 1px solid #ffffff26;
            border-radius: 50%;
            box-shadow:
                0 0 0 90px #ffffff06,
                0 0 0 180px #e306130d;
            content: "";
        }

        .brand {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand img {
            width: 62px;
            height: 70px;
            object-fit: contain;
        }

        .brand span {
            color: #bfc5cf;
            font-size: 12px;
        }

        .panel-copy {
            position: relative;
            z-index: 1;
            max-width: 460px;
        }

        .panel-copy h2 {
            font-size: clamp(32px, 4vw, 50px);
            font-weight: 800;
            letter-spacing: -.045em;
            line-height: 1.1;
        }

        .panel-copy p {
            margin-top: 18px;
            color: #bfc5cf;
            font-size: 15px;
            line-height: 1.7;
        }

        .content {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px clamp(22px, 5vw, 90px);
        }

        .card-validation {
            width: 100%;
            max-width: 610px;
        }

        .eyebrow {
            margin-bottom: 10px;
            color: var(--djezzy-red);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        h1 {
            margin-bottom: 10px;
            font-size: clamp(30px, 4vw, 40px);
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .lead-copy {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .status-box {
            margin-top: 28px;
            padding: 22px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 36px #141b260c;
        }

        .status-icon {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 13px;
            font-size: 21px;
        }

        .pending .status-icon {
            background: #fff4df;
            color: #92610c;
        }

        .refused .status-icon {
            background: #fff0f1;
            color: #b42332;
        }

        .btn-djezzy {
            border-color: var(--djezzy-red);
            background: var(--djezzy-red);
            color: #fff;
            font-weight: 700;
        }

        .btn-djezzy:hover {
            border-color: #b80813;
            background: #b80813;
            color: #fff;
        }

        .form-control {
            min-height: 46px;
            border-radius: 9px;
        }

        @media (max-width: 900px) {
            .validation-shell {
                grid-template-columns: 1fr;
            }

            .validation-panel {
                display: none;
            }

            .content {
                min-height: 100vh;
            }
        }
    </style>
</head>

<body>

<div class="validation-shell">

    <section class="validation-panel">
        <div class="brand">
            <img
                src="{{ asset('images/djezzy-logo.svg') }}"
                alt="Djezzy"
            >

            <span>
                Espace professionnel
            </span>
        </div>

        <div class="panel-copy">
            <h2>
                Votre dossier est en cours de validation.
            </h2>

            <p>
                Après la vérification de votre adresse e-mail,
                votre registre de commerce doit être contrôlé
                par un commercial avant l'activation complète
                de votre espace client.
            </p>
        </div>

        <div class="small text-white-50">
            DJEZZY · ESPACE CLIENT
        </div>
    </section>


    <main class="content">

        <div class="card-validation">

            @if (session('success'))
                <div
                    class="alert alert-success border-0 shadow-sm"
                    role="alert"
                >
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="eyebrow">
                Validation du compte
            </div>

            <h1>
                Bonjour {{ $client->name }}
            </h1>

            <p class="lead-copy">
                Votre adresse e-mail
                <strong>{{ $client->email }}</strong>
                est vérifiée.
            </p>


            @if (
                $client->statut_validation_compte
                === 'refuse'
            )

                <div class="status-box refused">

                    <div class="d-flex gap-3 align-items-start">
                        <div class="status-icon">
                            <i class="bi bi-x-lg"></i>
                        </div>

                        <div>
                            <h2 class="h5 fw-bold mb-2">
                                Registre de commerce non validé
                            </h2>

                            <p class="text-secondary mb-0">
                                Le commercial a refusé le document
                                transmis.
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-4 mb-0">
                        <strong>Motif :</strong><br>

                        {{ $client->motif_refus_validation }}
                    </div>


                    @if ($registre)
                        <a
                            href="{{ route(
                                'client.validation.registre'
                            ) }}"
                            target="_blank"
                            rel="noopener"
                            class="btn btn-outline-secondary w-100 mt-3"
                        >
                            <i class="bi bi-eye me-2"></i>
                            Consulter le document refusé
                        </a>
                    @endif


                    <form
                        method="POST"
                        action="{{ route(
                            'client.validation.replace-document'
                        ) }}"
                        enctype="multipart/form-data"
                        class="mt-4"
                    >
                        @csrf

                        <label
                            for="registre_commerce"
                            class="form-label fw-semibold"
                        >
                            Remplacer le registre de commerce
                        </label>

                        <input
                            id="registre_commerce"
                            type="file"
                            name="registre_commerce"
                            class="form-control
                                @error('registre_commerce')
                                    is-invalid
                                @enderror"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >

                        @error('registre_commerce')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            PDF, JPG, JPEG ou PNG —
                            maximum 5 Mo.
                        </div>

                        <button
                            type="submit"
                            class="btn btn-djezzy w-100 mt-3"
                        >
                            <i class="bi bi-upload me-2"></i>
                            Envoyer le nouveau document
                        </button>
                    </form>

                </div>

            @else

                <div class="status-box pending">

                    <div class="d-flex gap-3 align-items-start">
                        <div class="status-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                        <div>
                            <h2 class="h5 fw-bold mb-2">
                                En attente de validation
                            </h2>

                            <p class="text-secondary mb-0">
                                Votre registre de commerce doit
                                être vérifié par un commercial.
                                Vous pourrez accéder à votre espace
                                dès validation.
                            </p>
                        </div>
                    </div>

                    @if ($registre)
                        <div class="border rounded-3 p-3 mt-4">
                            <div class="small text-secondary">
                                Justificatif
                            </div>

                            <div class="fw-semibold mt-1">
                                Registre de commerce déposé
                            </div>

                            <a
                                href="{{ route(
                                    'client.validation.registre'
                                ) }}"
                                target="_blank"
                                rel="noopener"
                                class="btn btn-outline-secondary btn-sm mt-3"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Consulter mon document
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4 mb-0">
                            Aucun registre de commerce n'a été
                            retrouvé sur votre dossier.
                        </div>
                    @endif

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('logout') }}"
                class="mt-4"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-light border w-100"
                >
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Se déconnecter
                </button>
            </form>

        </div>

    </main>

</div>

</body>
</html>
