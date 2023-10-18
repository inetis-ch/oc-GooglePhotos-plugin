<?php

return [
    'plugin' => [
        'name' => 'Plugin Google Photos',
        'description' => 'Ce plugin permet d\'afficher des albums à partir de votre compte Google Photos (Picasa)'
    ],

    'permissions' => [
        'access_settings' => 'Accéder aux paramètres du plugin Google Photos',
        'tab' => 'Google Photos',
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
            'oAuthAppWarning' => 'Une configuration supplémentaire est requise',
            'oAuthAppWarningMessage' => 'Veuillez configurer une application OAuth afin de pouvoir utiliser ce plugin sur un domaine autre que localhost (<a href="https://octobercms.com/plugin/inetis-googlephotos" target="_blank">voir section "Prerequisites" du guide d\'installation</a>). Cliquer sur le bouton de connexion maintenant retournera probablement une erreur.',
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
            'thumbHeightTitle' => 'Hauteur',
            'thumbHeightDescription' => 'Hauteur maximale des miniatures. Laisser vide pour une hauteur automatique',
            'thumbWidthTitle' => 'Largeur',
            'thumbWidthDescription' => 'Largeur maximale des miniatures. Laisser vide pour une largeur automatique',
            'shouldCropTitle' => 'Rogner',
            'shouldCropDescription' => 'Faut-il rogner les miniatures pour correspondre à la taille spécifiée ou juste la redimensionner?',
            'pageSizeTitle' => 'Éléments par page',
            'pageSizeDescription' => 'Le nombre d\'éléments à afficher par page (0 pour désactiver la pagination)',
            'currentPageTitle' => 'Page actuelle',
            'currentPageDescription' => 'Le paramètre à utiliser comme position actuelle dans la pagination (commence à 1)',

            'optionYes' => 'Oui',
            'optionNo' => 'Non',
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
