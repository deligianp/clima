<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$aai = (isset($params['aai_auth'])) && ($params['aai_auth']);
$db2 = ($aai) ? require __DIR__ . '/db2.php' : ['class' => 'yii\db\Connection'];

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@webvimark' => '@vendor/webvimark/module-user-management/',
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                //'yii\bootstrap\BootstrapPluginAsset' => false,
                'webvimark\extensions\DateRangePicker\DateRangePickerAsset' => false,
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'R70KmWEXspuvizJt_q8FTNZkbkXu1Smh',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'webvimark\modules\UserManagement\components\UserConfig',

            // Comment this if you don't want to record user logins
            'on afterLogin' => function ($event) {
                \webvimark\modules\UserManagement\models\UserVisitLog::newVisitor($event->identity->id);
            }
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
            ],
        ],
        'log' => [
	    'traceLevel' => YII_DEBUG ? 3 : 0,
	    'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
		],
                [
	            'class' => 'yii\log\FileTarget',
		    'categories' => ['application'],
                    'levels' => ['error', 'warning', 'info','trace'],
                    'exportInterval' => 1,  // Export (write) the message to file after every log message
		    'logFile' => '@runtime/logs/debug.log',
                    'logVars' => [],
                    'maxFileSize' => 1024 * 2, // Maximum file size in kilobytes (2MB here)
		    'maxLogFiles' => 10, // Max number of log files before rotation
		    'except' => ['yii\db*']
                ]
            ],
        ],
        'db' => $db,
        'db2' => $db2,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'view' => (!$aai) ? [] : [
            'theme' => [
                'pathMap' => [
                    '@webvimark/views/auth' => '@app/views/user/'
                ],
            ],
        ],
    ],
    'modules' => [
        'user-management' => [
            'class' => 'webvimark\modules\UserManagement\UserManagementModule',

            // 'enableRegistration' => true,
            // 'rolesAfterRegistration' => ['Bronze'],

            // Add regexp validation to passwords. Default pattern does not restrict user and can enter any set of characters.
            // The example below allows user to enter :
            // any set of characters
            // (?=\S{8,}): of at least length 8
            // (?=\S*[a-z]): containing at least one lowercase letter
            // (?=\S*[A-Z]): and at least one uppercase letter
            // (?=\S*[\d]): and at least one number
            // $: anchored to the end of the string

            //'passwordRegexp' => '^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$',


            // Here you can set your handler to change layout for any controller or action
            // Tip: you can use this event in any module
            // 'on beforeAction'=>function(yii\base\ActionEvent $event) {
            //     if ( $event->action->uniqueId == 'user-management/auth/login' )
            //     {
            //         $event->action->controller->layout = 'loginLayout.php';
            //     };
            // },
        ],
        // 'ticket' => [
        //     'class' => ricco\ticket\Module::className(),
        // ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1','195.251.63.2'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1', '::1',],
    ];
}

return $config;
