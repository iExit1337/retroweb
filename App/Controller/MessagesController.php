<?php

namespace App\Controller;

use App\Model\Messages\Messages\MessagesFactory;
use App\Model\Messages\MessagesTopicsFactory;
use App\Model\Messages\MessageTopic;
use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\View\Template;
use System\Helpers\JSONWriter;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;
use System\Navigation\Point;
use System\Security\CSRF;

class MessagesController extends WebsiteController implements IRoutable
{

    /**
     * @var $_meTab Point
     */
    private $_meTab;
    /**
     * @var $_messagesTab Point
     */
    private $_messagesTab;

    public function onRegistration(): void
    {

        if ($this->minRank(1)) {
            $this->_meTab = $this->getNavigation()->getById("Me");
            $this->_messagesTab = new Point('Me.Messages', 'Nachrichten', $this->getApp()
                    ->getConfig()
                    ->get("site", "url") . "messages", 9998);

            $this->_meTab->add($this->_messagesTab);
        }
    }

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {

        return [
            new Route(RequestType::GET, '/messages', 'my-messages'),
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/messages/create', 'create-message'),
            new Route(RequestType::POST, '/ajax/messages/user', 'get-user-suggestions'),
            new Route([
                RequestType::POST,
                RequestType::GET
            ], '/messages/{id:\d+}', 'get-message'),
            new Route(RequestType::GET, '/messages/{id:\d+}/leave/{csrf_token}', 'leave')
        ];
    }

    private function myMessagesAction(): void
    {
        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate('messages/MyMessages.tpl.php');

        $this->setPageTitle('Meine Nachrichten');

        $this->addCSSFile('messages/MyMessages');

        $template->summary = function ($str, $limit = 100, $strip = false) {

            $str = ($strip == true) ? strip_tags($str) : $str;
            if (strlen($str) > $limit) {
                $str = substr($str, 0, $limit - 3);

                return (substr($str, 0, strrpos($str, ' ')) . '...');
            }

            return trim($str);
        };

        $template->messagesTopics = $this->getSession()->getUser()->getMessagesTopics();
        $this->display($template);
    }

    private function createMessageAction(Request $request): void
    {

        $template = $this->getView()->createTemplate('messages/CreateMessage.tpl.php');

        if ($request->getMethod() == RequestType::POST && CSRF::isValid($request->getPost("csrf_token"))) {
            $subject = $request->getPost("subject");
            $message = $request->getPost("message");
            $receiverIdsString = $request->getPost("receivers");

            if ($subject != null && $message != null && $receiverIdsString != null) {
                $subjectMinLength = (int)$this->getApp()->getConfig()->get("messages", "subject_min_length");
                $subjectMaxLength = (int)$this->getApp()->getConfig()->get("messages", "subject_max_length");

                if (strlen($subject) >= $subjectMinLength && strlen($subject) <= $subjectMaxLength) {
                    do {
                        $optimizedMessage = false;
                        $copiedMessage = str_replace([
                            "\n\n",
                            "\n \n"
                        ], "\n", $message);

                        if ($copiedMessage != $message) {
                            $message = $copiedMessage;
                            $optimizedMessage = true;
                        }
                    } while ($optimizedMessage);

                    $template->message = $optimizedMessage;

                    $messageMinLength = $this->getApp()->getConfig()->getInt("messages", "message_min_length");
                    $messageMaxLength = $this->getApp()->getConfig()->getInt("messages", "message_max_length");

                    if (strlen($message) >= $messageMinLength && strlen($message) <= $messageMaxLength) {
                        $receiverIds = explode(",", $receiverIdsString);
                        $receivers = [];
                        $userFactory = $this->getFactoryManager()->get(UserFactory::class);
                        foreach ($receiverIds as $receiverId) {
                            if (!isset($receivers[$receiverId])) {
                                /**
                                 * @var $user User|null
                                 */
                                $user = $userFactory->getById((int)$receiverId);
                                if ($user != null && $user->getInt("id") != $this->getSession()->getUser()->getInt("id")) {
                                    $profileSettings = $user->getProfileSettings();
                                    if ((bool)$profileSettings->getInt("allow_messages")) {
                                        $isBlocked = $user->hasBlocked($this->getSession()->getUser());
                                        if (!$isBlocked || ($isBlocked && (bool)$profileSettings->getInt("allow_messages_blocked_users"))) {
                                            $receivers[$receiverId] = $user;
                                        }
                                    }
                                }
                            }
                        }

                        $receiversCopy = $receivers;
                        $receivers = [];
                        foreach ($receiversCopy as $receiver) {
                            $receivers[] = $receiver;
                        }

                        if (count($receivers) > 0) {
                            $maxReceivers = (int)$this->getApp()->getConfig()->get("messages", "max_receivers");
                            if (count($receivers) <= $maxReceivers) {
                                /**
                                 * @var $messagesTopicsFactory MessagesTopicsFactory
                                 */
                                $messagesTopicsFactory = $this->getFactoryManager()->get(MessagesTopicsFactory::class);
                                $messagesTopicsFactory->createNewTopic($subject, $message, $receivers, $this->getSession()
                                    ->getUser());

                                $this->redirect("messages");
                            } else {
                                $template->error = "Eine Nachricht kann maximal " . $maxReceivers . " Empf&auml;nger haben.";
                            }
                        } else {
                            $template->error = "Du hast keine g&uuml;ltigen Empf&auml;nger angegeben.";
                        }
                    } else {
                        $template->error = "Die Nachricht muss zwischen " . $subjectMinLength . " und " . $subjectMaxLength . " Zeichen lang sein.";
                    }
                } else {
                    $template->error = "Der Betreff muss zwischen " . $subjectMinLength . " und " . $subjectMaxLength . " Zeichen lang sein.";
                }
            } else {
                $template->error = "Du hast nicht alle Felder ausgef&uuml;llt!";
            }
        }

        $template->subject = $request->getPost("subject");
        $template->message = $request->getPost("message");

        $this->addJSFile("selectize.jquery");
        $this->addJSFile("messages/Create");

        $this->addCSSFile("selectize");
        $this->addCSSFile("messages/Selectize");

        $this->setPageTitle('Neue Nachricht verfassen');

        $this->display($template);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    private function getUserSuggestions(Request $request): void
    {

        $jsonWriter = new JSONWriter(false);
        if (CSRF::isValid($request->getPost("token"), false)) {

            $term = $request->getPost("term");

            /**
             * @var $userFactory UserFactory
             */
            $userFactory = $this->getFactoryManager()->get(UserFactory::class);

            /**
             * @var $users User[]
             */
            $users = $userFactory->getWhereNameLike($term);

            foreach ($users as $user) {
                if ($user->getInt("id") != $this->getSession()->getUser()->getInt("id")) {
                    $profileSettings = $user->getProfileSettings();
                    if ((bool)$profileSettings->getInt("allow_messages")) {
                        $isBlocked = $user->hasBlocked($this->getSession()->getUser());
                        if (!$isBlocked || ($isBlocked && (bool)$profileSettings->getInt("allow_messages_blocked_users"))) {
                            $onlyFriends = (bool)$profileSettings->getInt("allow_messages_friends_only");
                            if (!$onlyFriends || ($onlyFriends && $user->hasFriendship($this->getSession()->getUser()))) {
                                $jsonWriter->write((object)[
                                    'value' => $user->getInt("id"),
                                    'text' => $user->get("username")
                                ]);
                            }
                        }
                    }
                }
            }
        }

        echo $jsonWriter;
    }

    private function showMessageAction(Request $request, array $vars): void
    {

        $template = $this->getView()->createTemplate('messages/Message.tpl.php');

        /**
         * @var $messageTopicFactory MessagesTopicsFactory
         */
        $messageTopicFactory = $this->getFactoryManager()->get(MessagesTopicsFactory::class);

        /**
         * @var $messageTopic MessageTopic
         */
        $messageTopic = $messageTopicFactory->getById($vars["id"]);
        if ($messageTopic == null) {
            $this->redirect("messages");
        }
        if (!$messageTopic->isSubscriber($this->getSession()->getUser())) {
            $this->redirect("messages");
        }

        if ($messageTopic->hasUnreadMessages($this->getSession()->getUser())) {
            $messageTopic->setUnreadMessagesAsRead($this->getSession()->getUser());
        }

        if ($request->getMethod() == RequestType::POST) {
            if (CSRF::isValid($request->getPost("csrf_token"))) {
                $message = $request->getPost("message");

                do {
                    $optimizedMessage = false;
                    $copiedMessage = str_replace([
                        "\n\n",
                        "\n \n"
                    ], "\n", $message);

                    if ($copiedMessage != $message) {
                        $message = $copiedMessage;
                        $optimizedMessage = true;
                    }
                } while ($optimizedMessage);

                $messageMinLength = $this->getApp()->getConfig()->getInt("messages", "message_min_length");
                $messageMaxLength = $this->getApp()->getConfig()->getInt("messages", "message_max_length");
                if (strlen($message) >= $messageMinLength && strlen($message) <= $messageMaxLength) {
                    /**
                     * @var $messagesFactory MessagesFactory
                     */
                    $messagesFactory = $this->getFactoryManager()->get(MessagesFactory::class);
                    $messagesFactory->_createObject([
                        'user_id' => $this->getSession()->getUser()->getInt("id"),
                        'message_topic_id' => $messageTopic->getInt("id"),
                        'message' => $message,
                        'timestamp' => time()
                    ], $this->getSession()->getUser());

                    $this->redirect('messages/' . $messageTopic->getInt("id"));
                } else {
                    $template->error = "Deine Nachricht muss zwischen " . $messageMinLength . " und " . $messageMaxLength . " Zeichen lang sein.";
                }
            }
        }

        $this->addCSSFile("messages/Message");
        $this->addCSSFile("perfect-scrollbar.min");

        $this->addJSFile("perfect-scrollbar.jquery.min");

        $this->setPageTitle("RE: " . $messageTopic->get("subject"));

        $template->messageTopic = $messageTopic;
        $this->display($template);
    }

    private function leaveMessage(int $messageTopicId, string $csrfToken): void
    {

        if (CSRF::isValid($csrfToken)) {
            /**
             * @var $messageTopicsFactory MessagesTopicsFactory
             */
            $messageTopicsFactory = $this->getFactoryManager()->get(MessagesTopicsFactory::class);
            /**
             * @var $messageTopic MessageTopic|null
             */
            $messageTopic = $messageTopicsFactory->getById($messageTopicId);
            if ($messageTopic != null) {
                if ($messageTopic->isSubscriber($this->getSession()->getUser())) {
                    $messageTopic->leave($this->getSession()->getUser());
                }
            }
        }

        $this->redirect("messages");
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        if (!$this->minRank(1)) {
            $this->redirect("");
        }

        $this->_meTab->setActive(true);
        $this->_messagesTab->setActive(true);

        if ($route->getHandler() == 'my-messages') {
            $this->myMessagesAction();
        } else {
            if ($route->getHandler() == 'create-message') {
                $this->createMessageAction($request);
            } else {
                if ($route->getHandler() == 'get-user-suggestions') {
                    $this->getUserSuggestions($request);
                } else {
                    if ($route->getHandler() == 'get-message') {
                        $this->showMessageAction($request, $vars);
                    } else {
                        if ($route->getHandler() == 'leave') {
                            $this->leaveMessage($vars['id'], $vars['csrf_token']);
                        }
                    }
                }
            }
        }
    }
}