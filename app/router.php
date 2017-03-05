<?php

use Phalcon\Mvc\Router;

// Create the router
$router = new Router();

$router->add(
    "sitemap",
    array(
        "controller" => "index",
        "action"     => "sitemapAction"
    )
);

$router->add(
    "{name}.{type:[a-z]+}",
    array(
        "controller" => "blog",
        "action"     => "showPost"
    )
);