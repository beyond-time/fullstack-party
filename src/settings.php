<?php
return [
    'settings' => [
        'gitHubClient' => [
            'id' => '',
            'secret' => '',
        ],
        'issues' => [
            'perPage' => 4,
        ],
        'testMode' => false, // Load 'octocat' data to have a lot of issues
        'displayErrorDetails' => false,
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache_path' => __DIR__ . '/../cache/',
        ],
    ],
];
