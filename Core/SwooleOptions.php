<?php

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

return [
    'MAIN_SERVER' => [
        'HOST'    => '0.0.0.0',
        'PORT'    => 9501,
        'SETTING' => [
            'daemonize'   => DAEMONIZE,
            'worker_num'  => 1,
            'max_request' => 5000,
            'log_file'    => ROOT . 'Runtime' . DIRECTORY_SEPARATOR . 'cron.log',
            'pid_file'    => ROOT . 'Runtime' . DIRECTORY_SEPARATOR . 'cron.pid',
        ]
    ]
];