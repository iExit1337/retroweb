<?php

namespace App\Controller;


use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;

class LogoutController extends WebsiteController implements IRoutable
{

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        $routes = [];
        if ($this->minRank(1)) {
            $routes[] = new Route(RequestType::GET, '/logout', 'logout');
        }

        return $routes;
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->getSession()->delete();
        $this->redirect("");
    }
}