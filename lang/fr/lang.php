<?php

return [
    'plugin' => [
        'name' => 'Plugin Google Photos',
        'description' => 'Ce plugin permet d\'afficher des albums à partir de votre compte Google Photos (Picasa)'
    ],

    'settings' => [
        'menuEntry' => [
            'label' => 'Google Photos',
            'description' => 'Paramètres du plugin Google Photos'
        ],
        'fields' => [
            'oAuthLabel' => 'Authentification GooglePhotos',
            'oAuthSignIn' => 'Connexion',
            'oAuthSignOut' => 'Déconnexion',
            'signOutConfirm' => 'Êtes-vous sûr de vouloir révoquer l\'accès de ce plugin à Google Photos?',
            'cacheDurationLabel' => 'Durée du cache (minutes)',
            'hiddenAlbumsLabel' => 'Albums masqués',
            'hiddenAlbumsDescription' => 'Vous pouvez indiquer ici une liste d\'albums à ignorer. Par exemple, vous pouvez masquer l\'album "Auto Backup" généré by Picasa.',
            'hiddenAlbumLabel' => 'Nom ou ID d\'album'
        ]
    ],

    'component' => [
        'albums' => [
            'name' => 'Liste d\'albums Google Photos',
            'description' => 'Affiche tous les albums du compte Google Photos'
        ],

        'album' => [
            'name' => 'Album Google Photos',
            'description' => 'Affiche un album du compte Google Photos'
        ],

        'fields' => [
            'albumPageTitle' => 'Page d\'album',
            'albumPageDescription' => 'La page à utiliser pour afficher le contenu d\'un album',
            'albumIdTitle' => 'ID d\'album',
            'albumIdDescription' => 'L\'ID de l\' album Google Photos à afficher',
            'visibilityTitle' => 'Visibilité',
            'visibilityDescription' => 'Le niveau de visibilité des albums à afficher',
            'thumbSizeTitle' => 'Taille des miniatures',
            'thumbSizeDescription' => 'La taille des miniatures à générer',
            'shouldCropTitle' => 'Miniatures carrées',
            'shouldCropDescription' => 'Faut-il couper au carré ou juste redimensionner les miniatures?',
            'cropModeTitle' => 'Dimension de redimensionnement',
            'cropModeDescription' => 'La dimension à utiliser avec le champ "Taille des miniatures" pour redimensionner ou découper les miniatures à générer',
            'pageSizeTitle' => 'Éléments par page',
            'pageSizeDescription' => 'Le nombre d\'éléments à afficher par page (0 pour désactiver la pagination)',
            'currentPageTitle' => 'Page actuelle',
            'currentPageDescription' => 'Le paramètre à utiliser comme position actuelle dans la pagination (commence à 1)',

            'optionAll' => 'Tout',
            'optionPublic' => 'Publique',
            'optionPrivate' => 'Privé',
            'optionVisible' => 'Visible',
            'optionYes' => 'Oui',
            'optionNo' => 'Non',
            'optionHeight' => 'Hauteur',
            'optionWidth' => 'Largeur',
            'optionSmallest' => 'Au plus petit',
            'optionLargest' => 'Au plus grand'
        ],

        'fieldsGroups' => [
            'pagination' => 'Pagination',
            'thumbnails' => 'Miniatures'
        ]
    ],

    'messages' => [
        'csrfMismatch' => 'Jeton CSRF invalide',
        'authSuccess' => 'Authentification réussie',
        'revokeSuccess' => 'Access révoqué avec succès',
        'revokeError' => 'Erreur lors de la révocation de l\'accès: :error'
    ]
];
