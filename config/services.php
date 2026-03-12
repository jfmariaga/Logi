<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

        /*
    |--------------------------------------------------------------------------
    | OnlyOffice DocSpace
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con OnlyOffice DocSpace (SaaS).
    | Puedes obtener estas credenciales desde el panel de tu DocSpace.
    |
    */
    'onlyoffice' => [
        // URL de tu DocSpace (ej: https://tu-empresa.onlyoffice.com)
        'docspace_url' => env('DOCSPACE_URL', ''),
        // Usuario administrador o con permisos de API
        'docspace_user' => env('DOCSPACE_USER', ''),
        // Contraseña del usuario
        'docspace_password' => env('DOCSPACE_PASSWORD', ''),
        // ID del room donde se subirán archivos temporales para edición (opcional)
        'docspace_room_id' => env('DOCSPACE_ROOM_ID', ''),
    ],
];
