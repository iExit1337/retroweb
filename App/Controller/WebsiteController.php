<?php


namespace App\Controller;

use App\Model\Alert\AlertFactory;
use App\Model\User\User;
use System\App\App;
use System\App\Controller\Controller;
use System\App\View\Template;
use System\App\Widget;
use System\Navigation\Navigation;

class WebsiteController extends Controller
{
    private $_pageTitle = "Habbo: ";

    private $_widgets = [];

    private $_files = [
        'css' => [],
        'js' => []
    ];

    private $_navigation;

    private $_includeHeader = true;

    private $_includeFooter = true;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->_navigation = Navigation::get();
        $this->_pageTitle = $app->getConfig()->get("site", "name") . ": ";

        $this->addCSSFile("reset");
        $this->addCSSFile("960_16_col");
        $this->addCSSFile("main");
        $this->addCSSFile("font-awesome.min");
        $this->addCSSFile("sweetalert");
        $this->addCSSFile("jquery-ui");

        $this->addJSFile("jquery-3.2.1.min");
        $this->addJSFile("sweetalert.min");
        $this->addJSFile("jquery-ui.min");
    }

    /**
     * @param string $className
     * @param bool $noCache
     * @return Widget
     */
    public function getWidget(string $className, bool $noCache = false): Widget
    {
        if ($noCache)
            return new $className($this->getView(), $this->getApp()->getConfig());

        if (!isset($this->_widgets[$className])) {
            $this->_widgets[$className] = new $className($this->getView(), $this->getApp()->getConfig());
        }

        return $this->_widgets[$className];
    }

    public function getNavigation(): Navigation
    {
        return $this->_navigation;
    }

    /**
     * @param bool $bool
     */
    public function includeHeader(bool $bool): void
    {
        $this->_includeHeader = $bool;
    }

    /**
     * @param bool $bool
     */
    public function includeFooter(bool $bool): void
    {
        $this->_includeFooter = $bool;
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->_pageTitle;
    }

    /**
     * @param string $file
     */
    public function addCSSFile(string $file): void
    {
        $filePath = $this->getApp()->getConfig()->get("site", "url") . 'public/css/' . $file . '.css?v=' . $this->getApp()->getConfig()->get('site', 'version');
        if (!in_array($filePath, $this->_files['css'])) {
            $this->_files['css'][] = $filePath;
        }
    }


    /**
     * @param string $file
     */
    public function addJSFile(string $file): void
    {
        $filePath = $this->getApp()->getConfig()->get("site", "url") . 'public/js/' . $file . '.js?v=' . $this->getApp()->getConfig()->get('site', 'version');
        if (!in_array($filePath, $this->_files['js'])) {
            $this->_files['js'][] = $filePath;
        }
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle(string $pageTitle): void
    {
        $this->_pageTitle .= $pageTitle;
    }

    /**
     * @param int $rank
     * @return bool
     */
    public function minRank(int $rank): bool
    {
        $user = $this->getSession()->getUser();

        if ($rank == -1 && $user != null) {
            return false;
        }

        if (($rank > 0 && $user == null) || ($user != null && $rank > $user->getInt("rank"))) {
            return false;
        }

        return true;
    }


    /**
     * @param string $url
     */
    public function redirect(string $url = ""): void
    {
        header("Location: " . $this->getApp()->getConfig()->get("site", "url") . $url);
        exit;
    }

    /**
     * @param Template $template
     */
    public function display(Template $template): void
    {
        $templates = [];

        /**
         * @var $sessionUser User|null
         */
        $sessionUser = $this->getSession()->getUser();

        if ($this->_includeHeader) $templates['header'] = $this->getView()->createTemplate("Header.tpl.php");
        $templates[] = $template;
        if ($this->_includeFooter) $templates['footer'] = $this->getView()->createTemplate("Footer.tpl.php");

        if ($this->_includeHeader) {
            $templates['header']->pageTitle = $this->_pageTitle;
            $templates['header']->files = $this->_files;
            $templates['header']->navigation = $this->_navigation;

            /**
             * @var $alertFactory AlertFactory
             */
            $alertFactory = $this->getFactoryManager()->get(AlertFactory::class);
            $templates['header']->alert = $alertFactory->getRandomAlert();

            if ($sessionUser != null) {
                $templates['header']->hasUnreadMessages = $sessionUser->hasUnreadMessages();
            }
        }

        foreach ($templates as $template) {
            $template->myUser = $sessionUser;
            $template->config = $this->getApp()->getConfig();

            if (!$this->_includeHeader) {
                $template->files = $this->_files;
            }

            $template->display();
        }
    }
}