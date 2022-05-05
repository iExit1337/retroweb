<?php


namespace System\Helpers\Hash;


class Hash
{

	/**
	 * @var string
	 */
    private $_hash;

	/**
	 * Hash constructor.
	 *
	 * @param string $hash
	 */
    public function __construct(string $hash)
    {
        $this->_hash = $hash;
    }

	/**
	 * @return string
	 */
    public function getHash(): string
    {
        return $this->_hash;
    }

	/**
	 * @param RawText $text
	 *
	 * @return bool
	 */
    public function equals(RawText $text): bool
    {
        return password_verify($text->getRawText(), $this->getHash());
    }
}