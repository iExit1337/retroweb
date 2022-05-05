<?php


namespace System\Helpers\Hash;


class HashConfig
{

	/**
	 * @var array
	 */
    public static $OPTIONS = [
        'cost' => 5
    ];

	/**
	 * @var int
	 */
    public static $ALGORITHM = PASSWORD_BCRYPT;
}