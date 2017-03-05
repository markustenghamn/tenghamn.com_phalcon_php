<?php

use Phalcon\Mvc\Controller;

/**
 * ErrorController
 */
class ErrorController extends Controller
{
    public function show404Action()
    {
        $this->response->setStatusCode(404, 'Not Found');
        return DOMHelper::method404();
    }
}