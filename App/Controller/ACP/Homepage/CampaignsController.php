<?php

namespace App\Controller\ACP\Homepage;

use App\Controller\ACP\ACPController;
use App\Model\Campaign\CampaignFactory;
use App\Widget\ACP\Navigation\Homepage\NavigationWidget;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;

class CampaignsController extends ACPController implements IRoutable
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
            new Route(RequestType::GET, "/admin/homepage/campaigns", "campaigns"),
            new Route(RequestType::GET, '/admin/homepage/campaigns/delete/{id:\d+}/{csrf_token}', 'delete-campaigns'),
            new Route([RequestType::GET, RequestType::POST], "/admin/homepage/campaigns/add", "add-campaigns")
        ];
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->onlyWithPermission("homepage_campaigns");

        $this->_homepageTab->setActive(true);
        $this->_adminTab->setActive(true);

        /**
         * @var $homepageNavigation NavigationWidget
         */
        $homepageNavigation = $this->getWidget(NavigationWidget::class);
        $homepageNavigation->setActive("ACP.Homepage.Campaigns");
        $this->addCSSFile("admin/Homepage/Campaigns/Campaigns");

        /**
         * @var $campaignFactory CampaignFactory
         */
        $campaignFactory = $this->getFactoryManager()->get(CampaignFactory::class);

        if ($route->getHandler() == "campaigns") {
            $this->setPageTitle("Campaigns");

            $template = $this->getView()->createTemplate("admin/Homepage/Campaigns/Campaigns.tpl.php");
            $template->navigation = $homepageNavigation;
            $template->campaigns = $campaignFactory->getCampaigns();

            $this->display($template);
        } elseif ($route->getHandler() == "add-campaigns") {
            $this->setPageTitle("Campaign Hinzufügen - Housekeeping");

            $campaignTitle = $request->getPost("campaign_title") ?? "";
            $campaignDesc = $request->getPost("campaign_desc") ?? "";
            $campaignUrl = $request->getPost("campaign_url") ?? "";
            $campaignImage = $request->getPost("campaign_image") ?? "";

            $template = $this->getView()->createTemplate("admin/Homepage/Campaigns/Add.tpl.php");

            if ($request->getMethod() == RequestType::POST && CSRF::isValid()) {
                if ($campaignTitle != null && $campaignDesc != null && $campaignImage != null) {
                    $campaign = $campaignFactory->createObject([
                        "title" => $campaignTitle,
                        "desc" => $campaignDesc,
                        "url" => $campaignUrl,
                        "image" => $campaignImage
                    ]);


                    if ($campaign != null) {
                        $template->success = "Campaign wurde erfolgreich angelegt";
                    } else {
                        $template->error = "Beim Anlegen des Campaigns ist ein Fehler aufgetreten. Versuchs erneut.";
                    }
                } else {
                    $template->error = "Bitte fülle alle Felder aus!";
                }

            }

            $template->navigation = $homepageNavigation;
            $template->campaignTitle = $campaignTitle;
            $template->campaignDesc = $campaignDesc;
            $template->campaignUrl = $campaignUrl;
            $template->campaignImage = $campaignImage;
            $this->display($template);

        } elseif ($route->getHandler() == "delete-campaigns") {
            if (CSRF::isValid($vars["csrf_token"])) {
                $campaign = $campaignFactory->getById($vars['id']);
                if ($campaign != null) {
                    $campaign->delete();
                }
            }

            $this->redirect("admin/homepage/campaigns");

        }
    }
}