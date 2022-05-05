<?php

namespace System;

class Config {

	/**
	 * @var array
	 */
	private $_data = [];

	/**
	 * Config constructor.
	 *
	 * @param string $file
	 */
	public function __construct(string $file) {

		$this->_data = parse_ini_file($file, true);
	}

    /**
     * @param string $section
     * @param string $key
     * @return null|string
     */
	public function get(string $section, string $key): ?string {

		return $this->_data[ $section ][ $key ] ?? null;
	}

    /**
     * @param string $section
     * @param string $key
     * @return int|null
     */
	public function getInt(string $section, string $key): ?int {

		return (int)$this->get($section, $key);
	}
}