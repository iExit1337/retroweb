<?php

namespace App\Controller\ACP\Homepage;


use App\Controller\ACP\ACPController;
use App\Model\News\News;
use App\Model\News\NewsFactory;
use App\Widget\ACP\Navigation\Homepage\NavigationWidget;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;

class NewsController extends ACPController implements IRoutable
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
            new Route(RequestType::GET, '/admin/homepage/news', 'news'),
            new Route([RequestType::GET, RequestType::POST], '/admin/homepage/news/add', 'add'),
            new Route([RequestType::GET, RequestType::POST], '/admin/homepage/news/edit/{id:\d+}', 'edit'),
            new Route(RequestType::GET, '/admin/homepage/news/delete/{id:\d+}/{csrf_token}', 'delete')
        ];
    }

    private function addNewsAction(Request $request, NavigationWidget $widget, NewsFactory $newsFactory): void
    {
        $template = $this->getView()->createTemplate("admin/Homepage/News/Add.tpl.php");

        $title = $request->getPost("title");
        $teaser = $request->getPost("teaser");
        $image = $request->getPost("image");
        $text = $request->getPost("text");
        $allowComments = (int)$request->getPost("allow_comments");
        $allowVoting = (int)$request->getPost("allow_voting");

        if ($request->getMethod() == RequestType::POST && CSRF::isValid($request->getPost("csrf_token"))) {
            if ($title != null && $teaser != null && $image != null && $text != null && ($allowVoting == 1 || $allowVoting == 0) && ($allowComments == 1 || $allowVoting == 0)) {
                $news = $newsFactory->createObject([
                    'user_id' => $this->getSession()->getUser()->getInt("id"),
                    'title' => $title,
                    'teaser' => $teaser,
                    'text' => $text,
                    'allow_commenting' => $allowComments,
                    'allow_voting' => $allowVoting,
                    'timestamp' => time(),
                    'image' => $image
                ]);

                if ($news != null) {
                    $this->redirect("articles");
                } else {
                    $this->redirect("admin/homepage/news/add");
                }
            } else {
                $template->error = "Du hast nicht alle Felder ausgef&uuml;llt";
            }
        }

        $this->setPageTitle("News verfassen");
        $this->addCSSFile("admin/Homepage/News/News");

        $template->navigation = $widget;

        $template->title = $title;
        $template->teaser = $teaser;
        $template->text = $text;
        $template->image = $image;
        $template->allow_comments = (bool)$allowComments;
        $template->allow_voting = (bool)$allowVoting;

        $this->display($template);
    }

    private function editNewsAction(Request $request, NavigationWidget $widget, News $news): void
    {
        $template = $this->getView()->createTemplate("admin/Homepage/News/Edit.tpl.php");

        if ($request->getMethod() == RequestType::POST && CSRF::isValid($request->getPost("csrf_token"))) {
            $title = $request->getPost("title");
            $teaser = $request->getPost("teaser");
            $image = $request->getPost("image");
            $text = $request->getPost("text");
            $allowComments = $request->getPost("allow_comments");
            $allowVoting = $request->getPost("allow_voting");

            $changedDataset = false;

            if ($title != $news->get("title") && !empty($title)) {
                $changedDataset = true;
                $news->set('title', $title);
            }

            if ($teaser != $news->get("teaser") && !empty($teaser)) {
                $changedDataset = true;
                $news->set('teaser', $teaser);
            }

            if ($image != $news->get("image") && !empty($image)) {
                $changedDataset = true;
                $news->set('image', $image);
            }

            if ($text != $news->get("text") && !empty($text)) {
                $changedDataset = true;
                $news->set('text', $text);
            }

            if (($allowComments == 1 || $allowComments == 0) && $allowComments != $news->getInt("allow_commenting")) {
                $news->set('allow_commenting', $allowComments);
                $changedDataset = true;
            }

            if (($allowVoting == 1 || $allowVoting == 0) && $allowVoting != $news->getInt("allow_voting")) {
                $news->set('allow_voting', $allowVoting);
                $changedDataset = true;
            }

            if ($changedDataset) {
                $template->success = "Die News wurden erfolgreich bearbeitet";
            } else {
                $template->error = "Du hast nichts ge&auml;ndert oder nicht alle Felder ausgef&uuml;llt";
            }
        }

        $template->id = $news->getInt("id");
        $template->title = $news->get("title");
        $template->teaser = $news->get("teaser");
        $template->text = $news->get("text");
        $template->image = $news->get("image");
        $template->allow_comments = (bool)$news->getInt("allow_commenting");
        $template->allow_voting = (bool)$news->getInt("allow_voting");

        $template->navigation = $widget;

        $this->setPageTitle("News verfassen");
        $this->addCSSFile("admin/Homepage/News/News");

        $this->display($template);
    }

    /**
     * @param Request $request
     * @param Route $route
     * @param array $vars
     * @throws \Exception
     */
    public function onRequest(Request $request, Route $route, array $vars): void
    {
        $this->onlyWithPermission("news");

        $this->_adminTab->setActive(true);
        $this->_homepageTab->setActive(true);

        /**
         * @var $homepageNavigation NavigationWidget
         */
        $homepageNavigation = $this->getWidget(NavigationWidget::class);
        $homepageNavigation->setActive("ACP.Homepage.News");

        /**
         * @var $newsFactory NewsFactory
         */
        $newsFactory = $this->getFactoryManager()->get(NewsFactory::class);

        if ($route->getHandler() == 'news') {

            $this->setPageTitle("News");

            $this->addCSSFile("admin/Homepage/News/List");

            $homepageNavigation = $this->getWidget(NavigationWidget::class);

            $template = $this->getView()->createTemplate("admin/Homepage/News/News.tpl.php");


            $template->navigation = $homepageNavigation;
            $template->newsList = $newsFactory->getLatestByLimit(50);

            $this->display($template);
            return;
        }

        if ($route->getHandler() == 'add') {
            $this->addNewsAction($request, $homepageNavigation, $newsFactory);
            return;
        }

        if ($route->getHandler() == 'edit') {
            /**
             * @var $news News|null
             */
            $news = $newsFactory->getById($vars['id']);
            if ($news != null) {
                $this->editNewsAction($request, $homepageNavigation, $news);
            } else {
                $this->redirect("admin/homepage/news");
            }
            return;
        }

        if ($route->getHandler() == 'delete') {
            /**
             * @var $news News|null
             */
            $news = $newsFactory->getById($vars['id']);
            if ($news != null) {
                if ($this->minRank($this->getMinRank("news_delete"))) {
                    $news->delete();
                }
            }
        }
    }
}