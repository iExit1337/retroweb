<?php


namespace System\Helpers\Hash;


class RawText
{

	/**
	 * @var null|string
	 */
    private $_hash = null;

	/**
	 * @var string
	 */
    private $_text;

	/**
	 * RawText constructor.
	 *
	 * @param string $text
	 */
    public function __construct(string $text)
    {
        $this->_text = $text;
    }

	/**
	 * @return string
	 */
    public function getRawText(): string
    {
        return $this->_text;
    }

	/**
	 * @return Hash
	 */
    public function getHash(): Hash
    {
        if($this->_hash == null)
        {
            $this->_hash = new Hash($this->generateHash());
        }

        return $this->_hash;
    }

	/**
	 * @param Hash $hash
	 *
	 * @return bool
	 */
    public function equals(Hash $hash): bool
    {
        return password_verify($this->_text, $hash->getHash());
    }

	/**
	 * @return string
	 */
    private function generateHash(): string
    {
        return password_hash($this->_text, HashConfig::$ALGORITHM, HashConfig::$OPTIONS);
    }

}