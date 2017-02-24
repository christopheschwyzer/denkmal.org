<?php

return function (CM_Config_Node $config) {
    $config->Denkmal_Http_Response_Api_Message->hashToken = 'very-secret';
    $config->Denkmal_Http_Response_Api_Message->hashAlgorithm = 'sha1';

    $config->Denkmal_Scraper_Manager->dayCount = 10;
    $config->Denkmal_Scraper_Date->defaultTimeHour = 22;

    $config->CM_App->setupScriptClasses[] = 'Denkmal_App_SetupScript_LoadLanguage';
    $config->CM_App->setupScriptClasses[] = 'Denkmal_App_SetupScript_Locations';

    $config->CM_Stream_Video->servers = array();

    $config->services['push-notification-sender'] = [
        'class'     => 'Denkmal_Push_Notification_Sender',
        'arguments' => [
            'clientConfig' => [
                'gcm_sender_id' => '<gcm-sender-id>',
            ]
        ]
    ];

    $config->services['google-cloud-messaging'] = [
        'class'     => '\CodeMonkeysRu\GCM\Sender',
        'arguments' => [
            'serverApiKey' => '<api-key>',
        ]
    ];

    $config->services['facebook'] = [
        'class'  => 'Denkmal_Facebook_ClientFactory',
        'method' => [
            'name'      => 'createClient',
            'arguments' => [
                'appId'     => '<app-id>',
                'appSecret' => '<app-secret>',
            ],
        ],
    ];

};
