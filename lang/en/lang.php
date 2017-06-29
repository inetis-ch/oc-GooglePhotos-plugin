<?php

return [
    'plugin' => [
        'name' => 'Google Photos plugin',
        'description' => 'This plugin allows to display albums from your Google Photos (Picasa) account'
    ],

    'settings' => [
        'menuEntry' => [
            'label' => 'Google Photos',
            'description' => 'Settings for Google Photos plugin'
        ],
        'fields' => [
            'oAuthLabel' => 'GooglePhotos Authentication',
            'oAuthSignIn' => 'Sign in',
            'oAuthSignOut' => 'Sign out',
            'signOutConfirm' => 'Are you sure you want to revoke your authorization token?',
            'cacheDurationLabel' => 'Cache duration (minutes)'
        ]
    ],

    'component' => [
        'albums' => [
            'name' => 'Google Photos albums list',
            'description' => 'Display all albums of the Google Photos account'
        ],

        'album' => [
            'name' => 'Google Photos album',
            'description' => 'Display one album of the Google Photos account'
        ],

        'fields' => [
            'albumPageTitle' => 'Album Page',
            'albumPageDescription' => 'The page that displays the detail of an album',
            'albumIdTitle' => 'Album ID',
            'albumIdDescription' => 'ID of the Google Photos album to show',
            'visibilityTitle' => 'Visibility',
            'visibilityDescription' => 'The visibility level of the albums to show',
            'thumbSizeTitle' => 'Thumbnail size',
            'thumbSizeDescription' => 'The height of the thumbnails to generate',
            'shouldCropTitle' => 'Square crop thumbnails',
            'shouldCropDescription' => 'Whether to crop or just resize thumbnails',
            'cropModeTitle' => 'Crop mode',
            'cropModeDescription' => 'The dimension to use with "Thumbnail size" when resizing or cropping the thumbnails',

            'optionAll' => 'All',
            'optionPublic' => 'Public',
            'optionPrivate' => 'Private',
            'optionVisible' => 'Visible',
            'optionYes' => 'Yes',
            'optionNo' => 'No',
            'optionHeight' => 'Height',
            'optionWidth' => 'Width',
            'optionSmallest' => 'Smallest',
            'optionLargest' => 'Largest'
        ]
    ],

    'messages' => [
        'csrfMismatch' => 'CSRF token mismatch',
        'authSuccess' => 'Authenticated successfully',
        'revokeSuccess' => 'Access revoked successfully',
        'revokeError' => 'Error trying to revoke access: :error'
    ]
];
