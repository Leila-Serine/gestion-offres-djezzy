<?php

/*
|--------------------------------------------------------------------------
| Patch automatique de la navigation
|--------------------------------------------------------------------------
|
| À lancer UNE FOIS depuis la racine du projet :
|
| php install_navigation.php
|
| Le script ajoute le lien "Validation clients" aux deux menus
| commercial (desktop + mobile) sans remplacer tout le layout.
|
*/

$path = __DIR__
    . '/resources/views/layouts/app.blade.php';

if (! file_exists($path)) {
    fwrite(
        STDERR,
        "Fichier introuvable : {$path}\n"
    );

    exit(1);
}

$content = file_get_contents($path);

$include = <<<'BLADE'
                @include('layouts.partials.client-validation-navigation')
BLADE;

if (
    str_contains(
        $content,
        "@include('layouts.partials.client-validation-navigation')"
    )
) {
    echo "Le lien Validation clients est déjà installé.\n";
    exit(0);
}

$needle =
    "@elseif (auth()->user()->role === 'commercial')";

$count = substr_count(
    $content,
    $needle
);

if ($count === 0) {
    fwrite(
        STDERR,
        "Bloc commercial introuvable dans le layout.\n"
    );

    exit(1);
}

$content = str_replace(
    $needle,
    $needle
        . PHP_EOL
        . PHP_EOL
        . $include,
    $content
);

file_put_contents(
    $path,
    $content
);

echo "Navigation mise à jour ({$count} bloc(s) commercial/commerciaux).\n";
