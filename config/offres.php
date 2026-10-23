<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Caractéristiques normalisées
    |--------------------------------------------------------------------------
    | Le commercial choisit le type ET l'unité dans des listes contrôlées.
    | On évite donc les variantes libres : GO / go / Go / jours / Jours...
    */
    'caracteristiques' => [
        'internet' => [
            'label' => 'Internet',
            'unites' => ['Mo', 'Go', 'To'],
            'unite_defaut' => 'Go',
        ],
        'duree' => [
            'label' => 'Durée / validité',
            'unites' => ['Minutes', 'Heures', 'Jours', 'Semaines', 'Mois'],
            'unite_defaut' => 'Jours',
        ],
        'appels' => [
            'label' => 'Appels',
            'unites' => ['Minutes', 'Heures'],
            'unite_defaut' => 'Minutes',
        ],
        'sms' => [
            'label' => 'SMS',
            'unites' => ['SMS'],
            'unite_defaut' => 'SMS',
        ],
        'credit' => [
            'label' => 'Crédit',
            'unites' => ['DA'],
            'unite_defaut' => 'DA',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Zones de stock du prototype
    |--------------------------------------------------------------------------
    */
    'zones' => [
        'Adrar', 'Chlef', 'Laghouat', 'Oum El Bouaghi', 'Batna', 'Béjaïa',
        'Biskra', 'Béchar', 'Blida', 'Bouira', 'Tamanrasset', 'Tébessa',
        'Tlemcen', 'Tiaret', 'Tizi Ouzou', 'Alger', 'Djelfa', 'Jijel',
        'Sétif', 'Saïda', 'Skikda', 'Sidi Bel Abbès', 'Annaba', 'Guelma',
        'Constantine', 'Médéa', 'Mostaganem', 'M’Sila', 'Mascara', 'Ouargla',
        'Oran', 'El Bayadh', 'Illizi', 'Bordj Bou Arréridj', 'Boumerdès',
        'El Tarf', 'Tindouf', 'Tissemsilt', 'El Oued', 'Khenchela',
        'Souk Ahras', 'Tipaza', 'Mila', 'Aïn Defla', 'Naâma',
        'Aïn Témouchent', 'Ghardaïa', 'Relizane', 'Timimoun',
        'Bordj Badji Mokhtar', 'Ouled Djellal', 'Béni Abbès', 'In Salah',
        'In Guezzam', 'Touggourt', 'Djanet', 'El M’Ghair', 'El Meniaa',
    ],
];
