<?php

return [
    'types' => [
        /*
         * 'default' => [
         *   'model' => App\Models\User::class, // model class
         *   'disk' => 'public', // required
         * ],
        */
        'voice' => [
            'model' => App\Models\VoiceFile::class,
            'disk' => 'public',
        ],

    ]
];
