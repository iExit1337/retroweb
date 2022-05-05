<?php


namespace App\Widget\Campaign;


use App\Model\Campaign\CampaignFactory;
use System\App\View\Template;
use System\App\Widget;

class CampaignWidget extends Widget
{

    private $_grid = 16;
    /**
     * @var $_campaignFactory CampaignFactory
     */
    private $_campaignFactory;

    /**
     * @return Template
     */
    protected function getTemplate(): Template
    {
        return $this->getView()->createTemplate("campaign/Campaign.tpl.php");
    }

    /**
     * @return array
     */
    protected function getCSSFiles(): array
    {
        return [
            "campaign/Campaign"
        ];
    }

    /**
     * @return array
     */
    protected function getJSFiles(): array
    {
        return [

        ];
    }

    public function getGrid(): int
    {
        return $this->_grid;
    }


    public function setCampaignFactory(CampaignFactory $campaignFactory): void
    {
        $this->_campaignFactory = $campaignFactory;
    }

    public function setGrid(int $grid): void
    {
        $this->_grid = $grid;
    }

    protected function onDisplay(): void
    {
        $this->set("campaigns", $this->_campaignFactory->getCampaigns());
        $this->set("grid", $this->_grid);
    }
}