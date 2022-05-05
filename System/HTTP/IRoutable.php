<?php


namespace System\HTTP;

use System\HTTP\Request\Request;

interface IRoutable
{
    /**
     * Returns the routes of the Controller
     * @return Route[]
     */
    public function getRoutes(): array;

    public function onRequest(Request $request, Route $route, array $vars): void;
}