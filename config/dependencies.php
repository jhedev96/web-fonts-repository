<?php

use Psr\Container\ContainerInterface;

/*
 * DIC configuration
 *
 * Docs: https://www.slimframework.com/docs/concepts/di.html
 */

$container = $app->getContainer();

// error handlers
$container['phpErrorHandler'] = function (ContainerInterface $c) {
    return new \Src\Handlers\PhpError($c->get('settings')['displayErrorDetails'], $c->get('logger'));
};
$container['errorHandler'] = function (ContainerInterface $c) {
    return $c->get('phpErrorHandler');
};

// view renderer
$container['renderer'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// csrf protect (beta)
$container['csrf'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['csrf'];
    return new Slim\Csrf\Guard($settings['prefix']);
};

// logger
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    return (new Apix\Log\Logger\File($settings['path']))->setMinLevel($settings['level']);
};

// webicons css code generator
$container['webiconCSSGenerator'] = function (ContainerInterface $c) {
    $icons = $c->get('settings')['icons'];
    $request = $c->get('request');
    return \Src\Services\WebiconCSSGenerator\WebiconCSSGenerator::createFromSettings($icons, $request->getUri()->getBasePath());
};

// webfonts css code generator
$container['webfontCSSGenerator'] = function (ContainerInterface $c) {
    $fonts = $c->get('settings')['fonts'];
    $request = $c->get('request');
    return \Src\Services\WebfontCSSGenerator\WebfontCSSGenerator::createFromSettings($fonts, $request->getUri()->getBasePath());
};

// webfonts css api code generator
$container['webfontCSSApiGenerator'] = function (ContainerInterface $c) {
    $method = $c->get('settings')['method'];
    return new \Src\Services\WebfontCSSApiGenerator\WebfontCSSApiGenerator($method['get']);
};
