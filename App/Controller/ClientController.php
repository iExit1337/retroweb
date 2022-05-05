<?php

namespace App\Controller;

use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;

class ClientController extends WebsiteController implements IRoutable
{

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/client', 'client')
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        if (!$this->minRank(1)) {
            $this->redirect();
        }

        $this->includeHeader(false);
        $this->includeFooter(false);

        $template = $this->getView()->createTemplate("client/Client.tpl.php");

        $this->display($template);
    }
}