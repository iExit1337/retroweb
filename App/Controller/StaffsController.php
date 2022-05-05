<?php


namespace App\Controller;


use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\View\Template;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;

class StaffsController extends WebsiteController implements IRoutable
{

    /**
     * @var $_communityTab Point
     */
    private $_communityTab;
    /**
     * @var $_staffsTab Point
     */
    private $_staffsTab;

    public function onRegistration(): void
    {
        $this->_communityTab = $this->getNavigation()->getById("Community");
        $this->_staffsTab = new Point("Community.Staffs", "Staffs", $this->getApp()->getConfig()->get("site", "url") . "community/staffs", 9997);

        $this->_communityTab->add($this->_staffsTab);
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            new Route(RequestType::GET, '/community/staffs', 'staffs')
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->_communityTab->setActive(true);
        $this->_staffsTab->setActive(true);

        $this->addCSSFile("staffs/Staffs");

        $this->setPageTitle("Staffs");

        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate('staffs/Staffs.tpl.php');

        /**
         * @var $userFactory UserFactory
         */
        $userFactory = $this->getFactoryManager()->get(UserFactory::class);
        /**
         * @var $staffs User[]
         */
        $staffs = $userFactory->getStaffs();

        $ranks = [];
        foreach ($staffs as $staff) {
            $rankString = $staff->getRankAsString();

            if (!isset($ranks[$rankString])) {
                $ranks[$rankString] = [];
            }

            $ranks[$rankString][] = $staff;
        }

        $template->ranks = $ranks;

        $this->display($template);
    }
}