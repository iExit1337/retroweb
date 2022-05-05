<?php

namespace System\Security;

use System\Config;
use System\HTTP\Request\Request;
use System\Session\Session;

class CSRF {

    /**
     * @var Session
     */
	private static $_session;

    /**
     * @var Config
     */
	private static $_config;

    /**
     * @var Request
     */
	private static $_request;

	private const FIELD_NAME = "csrf_token";

	private const SESSION_KEY = "csrf_token";

	/**
	 * @param Request $request
	 */
	public static function setRequest(Request $request): void {

		self::$_request = $request;
	}

	/**
	 * @param Session $session
	 * @param Config  $config
	 */
	public static function init(Session $session, Config $config): void {

		self::$_session = $session;
		self::$_config = $config;

		if (self::getToken() == null) {
			$session->set(self::SESSION_KEY, self::getNewToken());
		}
	}

	/**
	 * @return string
	 */
	private static function getNewToken(): string {

		return md5(md5(self::$_config->get("site", "csrf_hash")) . rand(0, 99999999) . md5(rand(1337, 6077)));
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public static function getField($name = self::FIELD_NAME): string {

		$token = self::getToken();

		return <<<FIELD
<input type="hidden" value="$token" name="$name">
FIELD;

	}

	/**
	 * @return null|string
	 */
	public static function getToken(): ?string {

		return self::$_session->get(self::SESSION_KEY);
	}

	/**
	 * @param null $token
	 * @param bool $reinitialize
	 *
	 * @return bool
	 */
	public static function isValid($token = null, $reinitialize = true): bool {

		$token = $token == null ? self::$_request->getPost(self::FIELD_NAME) : $token;
		if (self::getToken() == $token) {
			if ($reinitialize) {
				self::$_session->set(self::SESSION_KEY, self::getNewToken());
			}

			return true;
		}

		return false;
	}

}