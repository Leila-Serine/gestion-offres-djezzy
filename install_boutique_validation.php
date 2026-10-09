<?php

function fail_install(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

$root = __DIR__;

/*
|--------------------------------------------------------------------------
| 1. Enregistrer le provider
|--------------------------------------------------------------------------
*/

$providersPath = $root . '/bootstrap/providers.php';

if (! file_exists($providersPath)) {
    fail_install("Fichier introuvable : {$providersPath}");
}

$providers = file_get_contents($providersPath);

$providerLine =
    '    App\\Providers\\BoutiqueValidationServiceProvider::class,';

if (! str_contains(
    $providers,
    'BoutiqueValidationServiceProvider::class'
)) {
    $position = strrpos($providers, '];');

    if ($position === false) {
        fail_install(
            'Impossible de modifier bootstrap/providers.php.'
        );
    }

    $providers =
        substr($providers, 0, $position)
        . $providerLine
        . PHP_EOL
        . substr($providers, $position);

    file_put_contents(
        $providersPath,
        $providers
    );

    echo "Provider ajouté.\n";
} else {
    echo "Provider déjà présent.\n";
}

/*
|--------------------------------------------------------------------------
| 2. Navigation commercial + script commande
|--------------------------------------------------------------------------
*/

$layoutPath =
    $root . '/resources/views/layouts/app.blade.php';

if (! file_exists($layoutPath)) {
    fail_install("Fichier introuvable : {$layoutPath}");
}

$layout = file_get_contents($layoutPath);

if (! str_contains(
    $layout,
    'boutique-validation-navigation'
)) {
    $needle =
        "@elseif (auth()->user()->role === 'commercial')";

    $count = substr_count($layout, $needle);

    if ($count === 0) {
        fail_install(
            'Bloc commercial introuvable dans layouts/app.blade.php.'
        );
    }

    $include =
        "                @include('layouts.partials.boutique-validation-navigation')";

    $layout = str_replace(
        $needle,
        $needle
            . PHP_EOL
            . PHP_EOL
            . $include,
        $layout
    );

    echo "Navigation ajoutée dans {$count} bloc(s).\n";
} else {
    echo "Navigation déjà installée.\n";
}

if (! str_contains(
    $layout,
    'boutique-validation-order-script'
)) {
    $bodyPosition = strripos(
        $layout,
        '</body>'
    );

    if ($bodyPosition === false) {
        fail_install(
            'Balise </body> introuvable dans le layout.'
        );
    }

    $include =
        "    @include('layouts.partials.boutique-validation-order-script')";

    $layout =
        substr($layout, 0, $bodyPosition)
        . $include
        . PHP_EOL
        . PHP_EOL
        . substr($layout, $bodyPosition);

    echo "Contrôle visuel des commandes ajouté.\n";
} else {
    echo "Script commandes déjà installé.\n";
}

file_put_contents(
    $layoutPath,
    $layout
);

/*
|--------------------------------------------------------------------------
| 3. Fiche boutique client
|--------------------------------------------------------------------------
*/

$showPath =
    $root . '/resources/views/boutiques/show.blade.php';

if (! file_exists($showPath)) {
    fail_install("Fichier introuvable : {$showPath}");
}

$show = file_get_contents($showPath);

if (! str_contains(
    $show,
    'validation-justificatif'
)) {
    $endSectionPosition =
        strrpos($show, '@endsection');

    if ($endSectionPosition === false) {
        fail_install(
            '@endsection introuvable dans boutiques/show.blade.php.'
        );
    }

    $include =
        "@include('boutiques.partials.validation-justificatif')";

    $show =
        substr($show, 0, $endSectionPosition)
        . PHP_EOL
        . $include
        . PHP_EOL
        . PHP_EOL
        . substr($show, $endSectionPosition);

    file_put_contents(
        $showPath,
        $show
    );

    echo "Panneau validation ajouté à la fiche boutique.\n";
} else {
    echo "Panneau boutique déjà installé.\n";
}

echo PHP_EOL;
echo "Installation du code terminée.\n";
echo "Lance maintenant : php artisan migrate\n";
echo "Puis : php artisan optimize:clear\n";
echo "Puis : php artisan test --filter=BoutiqueValidationTest\n";
echo "Puis : php artisan test\n";
