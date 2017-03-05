<?php

require(__DIR__ . "/../vendor/autoload.php");

use Phalcon\Loader;
use Phalcon\Tag;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Mvc\Dispatcher as PhDispatcher;

ini_set('display_errors', '1');
ini_set('displaystartuperrors', 1);
error_reporting(E_ALL);
global $start;
$start = microtime(true);

try {

    $listener = new \Phalcon\Debug();
    $listener->listen(true, true);
    // Register an autoloader
    $loader = new Loader();
    $loader->registerDirs(
        array(
            '../app/controllers/',
            '../app/models/',
            '../app/classes/'
        )
    )->register();

    // Create a DI
    $di = new FactoryDefault();

    // Set the database service
    $di['db'] = function() {
        return new DbAdapter(array(
            "host"     => "localhost",
            "username" => "",
            "password" => "",
            "dbname"   => ""
        ));
    };

    // Setting up the view component
    $di['view'] = function() {
        $view = new View();
        $view->setViewsDir('../app/views/');
        return $view;
    };

    // Setup a base URI so that all generated URIs include the "tutorial" folder
    $di['url'] = function() {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    };

    // Setup the tag helpers
    $di['tag'] = function() {
        return new Tag();
    };

    $di->set('router', function(){
        $router = new Router();

        $router->add(
            "/sitemap/",
            array(
                "controller" => "Index",
                "action"     => "sitemap"
            )
        );

        $router->add(
            "/{name}",
            array(
                "controller" => "Blog",
                "action"     => "showpost"
            )
        );
        $router->add('/', 'Index::index');
        $router->handle();
        return $router;
    });

    $di->set(
        'dispatcher',
        function() use ($di) {

            $evManager = $di->getShared('eventsManager');

            $evManager->attach(
                "dispatch:beforeException",
                function($event, $dispatcher, $exception)
                {
                    switch ($exception->getCode()) {
                        case PhDispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case PhDispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(
                                array(
                                    'controller' => 'error',
                                    'action'     => 'show404',
                                )
                            );
                            return false;
                    }
                }
            );
            $dispatcher = new PhDispatcher();
            $dispatcher->setEventsManager($evManager);
            return $dispatcher;
        },
        true
    );

    // Handle the request
    $application = new Application($di);

    echo $application->handle()->getContent();

} catch (Exception $e) {
     echo "Exception: ", $e->getMessage();
}
