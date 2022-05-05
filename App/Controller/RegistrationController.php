<?php

namespace App\Controller;

use App\Model\Messages\MessagesTopicsFactory;
use App\Model\User\Currencies\CurrenciesFactory;
use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\View\Template;
use System\Helpers\Hash\Hash;
use System\Helpers\Hash\RawText;
use System\HTTP\IRoutable;
use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;
use System\HTTP\Route;

class RegistrationController extends WebsiteController implements IRoutable
{

    /**
     * Returns the routes of the Controller
     * @return array
     */
    public function getRoutes(): array
    {

        return [
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/registration', 'step-1'),
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/registration/profile-info', 'step-2'),
            new Route([
                RequestType::GET,
                RequestType::POST
            ], '/registration/day-of-birth', 'step-3'),

            new Route(RequestType::GET, '/registration/abort', 'abort'),
            new Route(RequestType::GET, '/registration/error', 'error')
        ];
    }

    /**
     * E-Mail and Password
     *
     * @param Request $request
     */
    private function setPasswordAndMailStep(Request $request): void
    {

        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("registration/Step-1.tpl.php");
        $template->title = "E-Mail & Passwort - Registration";

        $errors = [
            'mail' => null,
            'password' => null
        ];

        $mail = $request->getPost("email");

        if ($request->getMethod() == RequestType::POST) {
            $success = 0;

            $passwordMinLength = $this->getApp()->getConfig()->getInt("registration", "min_password_length");
            $passwordMaxLength = $this->getApp()->getConfig()->getInt("registration", "max_password_length");

            $password = $request->getPost("password");
            $password_repeat = $request->getPost("password_repeat");

            if (strlen($password) >= $passwordMinLength && strlen($password) <= $passwordMaxLength) {
                if ($password == $password_repeat) {
                    ++$success;
                } else {
                    $errors['password'] = 'Deine Passw&ouml;rter stimmen nicht &uuml;berein.';
                }
            } else {
                $errors['password'] = 'Dein Passwort muss zwischen ' . $passwordMinLength . ' und ' . $passwordMaxLength . ' Zeichen lang sein.';
            }

            if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                /**
                 * @var $userFactory UserFactory
                 */
                $userFactory = $this->getFactoryManager()->get(UserFactory::class);
                /**
                 * @var $user User|null
                 */
                $user = $userFactory->getByColumn("mail", $mail);
                if ($user == null) {
                    ++$success;
                } else {
                    $errors['mail'] = "Diese E-Mail ist bereits vergeben.";
                }
            } else {
                $errors['mail'] = "Bitte gib eine g&uuml;ltige E-Mail Adresse an.";
            }

            if ($success == 2) {
                $this->getSession()->set("register_mail", $mail);
                $this->getSession()->set("register_password", $password);
                $this->getSession()->set("registration_step", 2);

                $this->redirect("registration/profile-info");
            }
        }

        $template->mail = $mail;
        $template->errors = $errors;

        $this->display($template);
    }

    private function setUsernameStep(Request $request): void
    {
        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("registration/Step-2.tpl.php");
        $template->title = "Username - Registration";

        $errors = [
            'username' => null
        ];

        if ($request->getMethod() == RequestType::POST) {
            $usernameMinLength = (int)$this->getApp()->getConfig()->get("registration", "min_username_length");
            $usernameMaxLength = (int)$this->getApp()->getConfig()->get("registration", "max_username_length");

            $username_regex = $this->getApp()->getConfig()->get("registration", "username_regex");

            $username = $request->getPost("username");
            if (strlen($username) >= $usernameMinLength && strlen($username) <= $usernameMaxLength) {
                if (preg_match($username_regex, $username) != 0) {
                    /**
                     * @var $userFactory UserFactory
                     */
                    $userFactory = $this->getFactoryManager()->get(UserFactory::class);
                    /**
                     * @var $user User|null
                     */
                    $user = $userFactory->getByColumn("username", $username);
                    if ($user == null) {
                        $this->getSession()->set("register_username", $username);
                        $this->getSession()->set("registration_step", 3);

                        $this->redirect("registration/day-of-birth");
                    } else {
                        $errors['username'] = 'Dieser Username ist bereits vergeben.';
                    }
                } else {
                    $errors['username'] = 'Dein Username enth&auml;lt ung&uuml;ltige Zeichen.';
                }
            } else {
                $errors['username'] = 'Dein Username muss zwischen ' . $usernameMinLength . ' und ' . $usernameMaxLength . ' Zeichen lang sein.';
            }

        }

        $template->errors = $errors;

        $this->display($template);
    }

    private function setDayOfBirthStep(Request $request): void
    {
        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("registration/Step-3.tpl.php");
        $template->title = "Geburtsdatum - Registration";

        $errors = [
            'birth' => null
        ];

        if ($request->getMethod() == RequestType::POST) {
            $day = (int)$request->getPost("day");
            $month = (int)$request->getPost("month");
            $year = (int)$request->getPost("year");

            if (($day > 0 && $day < 32) && ($month > 0 && $month < 13) && ($year >= 1950 && $year < (int)date('Y'))) {
                $timestamp = strtotime($day . "." . $month . "." . $year);
                $username = $this->getSession()->get("register_username");
                $mail = $this->getSession()->get("register_mail");
                $password = $this->getSession()->get("register_password");

                $this->getSession()->delete();

                /**
                 * @var $userFactory UserFactory
                 */
                $userFactory = $this->getFactoryManager()->get(UserFactory::class);
                if ($userFactory->getByColumn("username", $username) == null && $userFactory->getByColumn("mail", $mail) == null) {

                    $config = $this->getApp()->getConfig();

                    $sessionKey = $this->getApp()->getSession()->generateSessionKey();
                    /**
                     * @var $user User|null
                     */
                    $user = $userFactory->createObject([
                        'username' => $username,
                        'mail' => $mail,
                        'password' => (new RawText($password))->getHash()->getHash(),
                        'account_day_of_birth' => $timestamp,
                        'account_created' => time(),
                        'ip_register' => $_SERVER["REMOTE_ADDR"],
                        'ip_current' => $_SERVER["REMOTE_ADDR"],
                        'last_login' => time(),
                        'credits' => $config->getInt("registration", "credits"),
                        'pixels' => $config->getInt("registration", "pixels"),
                        'points' => $config->getInt("registration", "points"),
                        'motto' => $config->get("registration", "motto"),
                        'look' => $config->get("registration", "look"),
                        'gender' => 'M',
                        'online' => 0,
                        'home_room' => $config->getInt("registration", "home_room"),
                        'rank' => $config->getInt("registration", "rank"),
                        'session_key' => $sessionKey,
                        'last_daily_timestamp' => time()
                    ]);

                    if ($user != null) {
                        $user->createAccountSettings();
                        $user->createProfileSettings();

                        /**
                         * @var $currenciesFactory CurrenciesFactory
                         */
                        $currenciesFactory = $this->getFactoryManager()->get(CurrenciesFactory::class);

                        // pixels
                        $currenciesFactory->createObject([
                            'user_id' => $user->getInt("id"),
                            'type' => 0,
                            'amount' => $config->getInt("registration", "pixels"),
                            'id' => $currenciesFactory->getNewId()
                        ]);

                        $this->getSession()->set("habbo_username", $username);
                        $this->getSession()->set("session_key", (new RawText($sessionKey))->getHash()->getHash());

                        $welcomeMessage = (bool)$config->getInt("registration", "welcome_message");
                        if ($welcomeMessage) {
                            $message = str_replace([
                                "\\n",
                                "%username%"
                            ], [
                                "\n",
                                $user->get("username")
                            ], $config->get("welcome_message", "message"));
                            $creatorId = $config->getInt("welcome_message", "user_id");
                            $subject = $config->get("welcome_message", "subject");

                            $receivers = [$user];

                            /**
                             * @var $messagesTopicsFactory MessagesTopicsFactory
                             */
                            $messagesTopicsFactory = $this->getFactoryManager()->get(MessagesTopicsFactory::class);
                            /**
                             * @var $messageSender User
                             */
                            $messageSender = $userFactory->getById($creatorId);
                            $messagesTopicsFactory->createNewTopic($subject, $message, $receivers, $messageSender);
                        }

                        $this->redirect("me");
                    } else {
                        $this->redirect("registration/error");
                    }
                } else {
                    $this->redirect("registration/error");
                }
            } else {
                $errors['birth'] = 'Bitte gib ein g&uuml;ltiges Geburtsdatum an.';
            }
        }

        $template->errors = $errors;

        $this->display($template);
    }

    private function errorAction(): void
    {
        /**
         * @var $template Template
         */
        $template = $this->getView()->createTemplate("registration/Error.tpl.php");
        $template->title = "Oops - Registration";

        $this->display($template);
    }

    public function onRequest(Request $request, Route $route, array $vars): void
    {

        if (!$this->minRank(-1)) {
            $this->redirect("me");
        }

        if ($route->getHandler() == 'abort') {
            $this->getSession()->delete();
            $this->redirect("");
        }

        $this->includeHeader(false);
        $this->includeFooter(false);

        $this->addCSSFile('registration/Registration');

        if ($route->getHandler() == 'error') {
            $this->errorAction();

            return;
        }

        if ($route->getHandler() == 'step-1') {
            if ($this->getSession()->get("registration_step") != null) {
                $this->redirect("registration/profile-info");
            }

            $this->setPasswordAndMailStep($request);

            return;
        }

        if ($route->getHandler() == 'step-2') {
            if ($this->getSession()->get("registration_step") == null) {
                $this->redirect("registration");
            } else {
                if ($this->getSession()->get("registration_step") == 3) {
                    $this->redirect("registration/day-of-birth");
                }
            }

            $this->setUsernameStep($request);

            return;
        }

        if ($route->getHandler() == 'step-3') {
            if ($this->getSession()->get("registration_step") == null) {
                $this->redirect("registration");
            } else {
                if ($this->getSession()->get("registration_step") == 2) {
                    $this->redirect("registration/profile-info");
                }
            }

            $this->setDayOfBirthStep($request);

            return;
        }
    }
}