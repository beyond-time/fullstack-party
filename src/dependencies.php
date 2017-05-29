<?php

$container = $app->getContainer();

$container['gitHubAuth'] = new \GitHub\Auth($container->get('settings')['gitHubClient']);

$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};
