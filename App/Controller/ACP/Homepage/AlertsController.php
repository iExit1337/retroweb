<?php
namespace App\Controller\ACP\Homepage;

use App\Controller\ACP\ACPController;
use App\Model\Alert\AlertFactory;
use App\Widget\ACP\Navigation\Homepage\NavigationWidget;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;

class AlertsController extends ACPController implements IRoutable
{

    /**
     * @var $_homepageTab Point
     */
    private $_homepageTab;
    /**
     * @var $_adminTab Point
     */
    private $_adminTab;

    public function onRegistration(): void
    {
        parent::onRegistration();

        if ($this->minRank($this->getMinRank("acp_tab")) && $this->minRank($this->getMinRank("homepage_tab"))) {
            $this->_homepageTab = $this->getNavigationPoint()->getById("ACP.Homepage");
            $this->_adminTab = $this->getNavigationPoint();
        }
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/admin/homepage/alerts', 'alerts'),
            new Route(RequestType::GET, '/admin/homepage/alerts/toggle/{id:\d+}/{csrf_token}', 'toggle-alert'),
            new Route(RequestType::GET, '/admin/homepage/alerts/delete/{id:\d+}/{csrf_token}', 'delete-alert'),
            new Route([RequestType::GET, RequestType::POST], '/admin/homepage/alerts/add', 'add-alert')
        ];
    }

    /**
     * @param Request $request
     * @param Route $route
     * @param array $vars
     * @throws \Exception
     */
    public function onRequest(Request $request, Route $route, array $vars): void
    {

        $this->onlyWithPermission("homepage_alerts");

        $this->_homepageTab->setActive(true);
        $this->_adminTab->setActive(true);

        /**
         * @var $homepageNavigation NavigationWidget
         */
        $homepageNavigation = $this->getWidget(NavigationWidget::class);
        $homepageNavigation->setActive("ACP.Homepage.Alerts");

        /**
         * @var $alertsFactory AlertFactory
         */
        $alertsFactory = $this->getFactoryManager()->get(AlertFactory::class);

        if ($route->getHandler() == 'alerts') {
            $this->setPageTitle("Alerts");

            $template = $this->getView()->createTemplate("admin/Homepage/Alerts/Alerts.tpl.php");

            $this->addCSSFile("admin/Homepage/Alerts/Alerts");

            $template->activeAlerts = $alertsFactory->getActiveAlerts();
            $template->inactiveAlerts = $alertsFactory->getInactiveAlerts();
            $template->navigation = $homepageNavigation;

            $this->display($template);
        } else if ($route->getHandler() == 'toggle-alert') {
            if (CSRF::isValid($vars["csrf_token"])) {
                $alert = $alertsFactory->getById($vars['id']);
                if ($alert != null) {
                    $alert->set("active", $alert->getInt("active") == 1 ? 0 : 1);
                }
            }

            $this->redirect('admin/homepage/alerts');
        } else if ($route->getHandler() == 'delete-alert') {
            if (CSRF::isValid($vars["csrf_token"])) {
                $alert = $alertsFactory->getById($vars['id']);
                if ($alert != null) {
                    $alert->delete();
                }
            }

            $this->redirect('admin/homepage/alerts');
        } else if ($route->getHandler() == 'add-alert') {
            $this->setPageTitle("Alert hinzufügen - Housekeeping");

            $active = true;
            $type = 1;
            $text = "";

            $template = $this->getView()->createTemplate("admin/Homepage/Alerts/Add.tpl.php");

            if ($request->getMethod() == RequestType::POST && CSRF::isValid($request->getPost("csrf_token"))) {
                $text = $request->getPost("text");
                $postType = (int)$request->getPost("type");

                $postActive = (bool)(int)$request->getPost("active");
                $active = $postActive;

                if ($postType == 1 || $postType == 2) {
                    $type = $postType;
                }

                $minLength = $this->getApp()->getConfig()->getInt("alerts", "min_length");
                $maxLength = $this->getApp()->getConfig()->getInt("alerts", "max_length");
                $length = strlen($text);

                if ($length >= $minLength && $length <= $maxLength) {
                    $alertsFactory->createObject([
                        'text' => $text,
                        'active' => (int)$active,
                        'type' => $type
                    ]);

                    $this->redirect("admin/homepage/alerts");
                } else {
                    $template->error = "Der Text muss zwischen " . $minLength . " und " . $maxLength . " Zeichen lang sein.";
                }
            }

            $template->navigation = $homepageNavigation;
            $template->active = $active;
            $template->type = $type;
            $template->text = $text;

            $this->display($template);
        }
    }
}