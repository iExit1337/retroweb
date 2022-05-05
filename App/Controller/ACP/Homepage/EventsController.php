<?php

namespace App\Controller\ACP\Homepage;


use App\Controller\ACP\ACPController;
use App\Model\Event\EventFactory;
use App\Widget\ACP\Navigation\Homepage\NavigationWidget;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;


class EventsController extends ACPController implements IRoutable
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
            new Route(RequestType::GET, '/admin/homepage/events', 'events'),
            new Route(RequestType::GET, '/admin/homepage/events/delete/{id:\d+}/{csrf_token}', 'delete-event'),
            new Route([RequestType::GET, RequestType::POST], '/admin/homepage/events/add', 'add-event')
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        $this->onlyWithPermission("homepage_events");

        $this->_homepageTab->setActive(true);
        $this->_adminTab->setActive(true);

        /**
         * @var $homepageNavigation NavigationWidget
         */
        $homepageNavigation = $this->getWidget(NavigationWidget::class);
        $homepageNavigation->setActive('ACP.Homepage.Events');
        $this->addCSSFile("admin/Homepage/Events/Events");

        /**
         * @var $eventsFactory EventFactory
         */
        $eventsFactory = $this->getFactoryManager()->get(EventFactory::class);

        if ($route->getHandler() == 'events') {
            $this->setPageTitle("Events");

            $template = $this->getView()->createTemplate('admin/Homepage/Events/Events.tpl.php');
            $template->navigation = $homepageNavigation;
            $template->activeEvents = $eventsFactory->getActiveEvents();
            $template->upcomingEvents = $eventsFactory->getUpcomingEvents();

            $this->display($template);
        } elseif ($route->getHandler() == 'delete-event') {
            if (CSRF::isValid($vars["csrf_token"])) {
                $events = $eventsFactory->getById($vars['id']);
                if ($events != null) {
                    $events->delete();
                }
            }

            $this->redirect("admin/homepage/events");

        } elseif ($route->getHandler() == 'add-event') {
            $this->setPageTitle("Event hinzufügen - Housekeeping");

            $eventName = "";
            $eventDesc = "";
            $startDate = date("Y-m-d");
            $startTime = date("H:i");
            $endDate = date("Y-m-d");
            $endTime = date("H:i");

            $template = $this->getView()->createTemplate('admin/Homepage/Events/Add.tpl.php');

            if ($request->getMethod() == RequestType::POST && CSRF::isValid()) {
                $eventName = $request->getPost("event_name");
                $eventDesc = $request->getPost("event_desc");
                $startDate = $request->getPost("start_date");
                $startTime = $request->getPost("start_time");
                $endDate = $request->getPost("end_date");
                $endTime = $request->getPost("end_time");

                if ($eventName != null && $eventDesc != null && $startDate != null && $startTime != null && $endDate != null && $endTime != null) {
                    $unixTimeStart = \DateTime::createFromFormat("Y-m-d H:i", $startDate . " " . $startTime)->getTimestamp();
                    $unixTimeEnd = \DateTime::createFromFormat("Y-m-d H:i", $endDate . " " . $endTime)->getTimestamp();

                    if ($unixTimeStart > time()) {
                        if ($unixTimeStart < $unixTimeEnd) {
                            $event = $eventsFactory->createObject([
                                "title" => $eventName,
                                "desc" => $eventDesc,
                                "start_time" => $unixTimeStart,
                                "end_time" => $unixTimeEnd,
                                "user_id" => $this->getSession()->getUser()->getInt("id")
                            ]);

                            if ($event != null) {
                                $template->success = true;
                            } else {
                                $template->error = "Beim Anlegen des Events ist ein Fehler aufgetreten. Versuchs erneut.";
                            }
                        } else {
                            $template->error = "Das Event muss nach dem Start enden.";
                        }
                    } else {
                        $template->error = "Das Event muss in der Zukunft starten!";
                    }
                } else {
                    $template->error = "Bitte fülle alle Felder aus!";
                }

            }

            $template->navigation = $homepageNavigation;
            $template->eventName = $eventName;
            $template->eventDesc = $eventDesc;
            $template->startDate = $startDate;
            $template->startTime = $startTime;
            $template->endDate = $endDate;
            $template->endTime = $endTime;
            $this->display($template);

        }
    }
}