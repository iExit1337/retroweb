<?php

namespace System\Session;

use App\Model\User\User;
use App\Model\User\UserFactory;
use System\App\Model\FactoryManager;
use System\Helpers\Hash\Hash;
use System\Helpers\Hash\RawText;

class Session
{

    public const NOT_SET = -1;

    public const NOT_LOGGED_IN = 0;

    public const LOGGED_IN = 1;

    /**
     * @var FactoryManager
     */
    private $_factoryManager;

    /**
     * @var User|null
     */
    private $_user = null;

    private $_data = [];

    /**
     * @var int
     */
    private $_userState = self::NOT_SET;

    /**
     * Session constructor.
     *
     * @param FactoryManager $factoryManager
     */
    public function __construct(FactoryManager $factoryManager)
    {
        $this->_factoryManager = $factoryManager;
        $this->_data =& $_SESSION;

        if ($this->getSessionKey() == null) {
            $rawSessionKey = $this->generateSessionKey();
            $this->set("session_key", (new RawText($rawSessionKey))->getHash()->getHash());
        }
    }

    /**
     * @return string
     */
    public function generateSessionKey(): string
    {
        return md5($_SERVER["REMOTE_ADDR"] . time() . md5(rand(0, 9999999)));
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name): ?string
    {
        return $this->get($name);
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key): ?string
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * @param null|string $key
     */
    public function delete(?string $key = null): void
    {
        if ($key == null) {
            foreach ($this->_data as $key => $value) {
                unset($this->_data[$key]);
            }
        } else {
            if ($this->get($key) != null) {
                unset($this->_data[$key]);
            }
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set(string $name, string $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value): void
    {
        $this->_data[$key] = $value;
    }

    /**
     * @return null|string
     */
    public function getSessionKey(): ?string
    {
        return $this->get("session_key");
    }

    public function getUser(): ?User
    {
        if (!($this->_user instanceof User) && $this->_userState == self::NOT_SET) {
            if (($username = $this->get("habbo_username")) != null && $this->getSessionKey() != null) {
                /**
                 * @var $userFactory UserFactory
                 */
                $userFactory = $this->_factoryManager->get(UserFactory::class);
                /**
                 * @var $user User|null
                 */
                $user = $userFactory->getByColumn("username", $username);

                if ($user != null) {
                    $sessionKeyDatabase = new RawText($user->get("session_key"));
                    $sessionKey = new Hash($this->getSessionKey());
                    if ($sessionKey->equals($sessionKeyDatabase) && $sessionKeyDatabase->equals($sessionKey)) {
                        $this->_user = $user;
                        $this->_userState = self::LOGGED_IN;
                    } else {
                        $this->delete("session_key");
                        $this->delete("habbo_username");
                        $this->_userState = self::NOT_LOGGED_IN;
                        $this->_user = null;
                    }
                } else {
                    $this->_userState = self::NOT_LOGGED_IN;
                }
            } else {
                $this->_userState = self::NOT_LOGGED_IN;
            }
        }

        return $this->_user;
    }
}