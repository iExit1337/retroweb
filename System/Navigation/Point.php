<?php


namespace System\Navigation;


class Point
{

	/**
	 * @var string
	 */
    private $_id;

	/**
	 * @var string
	 */
    private $_text;

	/**
	 * @var string
	 */
    private $_url;

	/**
	 * @var int
	 */
    private $_position;

	/**
	 * @var bool
	 */
    private $_isActive = false;

	/**
	 * @var bool
	 */
    private $_hasChildren = true;

	/**
	 * @var array
	 */
    private $_navigationPoints = [];

	/**
	 * Point constructor.
	 *
	 * @param string $id
	 * @param string $text
	 * @param string $url
	 * @param int    $position
	 */
    public function __construct(string $id, string $text, string $url, int $position)
    {
        $this->_id = $id;
        $this->_text = $text;
        $this->_url = $url;
        $this->_position = $position;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * @return array
     */
    public function getNavigationPoints(): array
    {
        usort($this->_navigationPoints, function (Point $a, Point $b) {
            if ($a->getPosition() > $b->getPosition()) {
                return -1;
            } else if ($a->getPosition() < $b->getPosition()) {
                return 1;
            }
            return 0;
        });

        return $this->_navigationPoints;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->_position;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->_text;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->_url;
    }

	/**
	 * @param bool $bool
	 */
    public function setActive(bool $bool): void
    {
        $this->_isActive = $bool;
    }

	/**
	 * @return bool
	 */
    public function isActive(): bool
    {
        return $this->_isActive;
    }

	/**
	 * @param bool $bool
	 */
    public function hasChildren(bool $bool): void
    {
        $this->_hasChildren = $bool;
    }

	/**
	 * @param string $id
	 *
	 * @return null|Point
	 */
    public function getById(string $id): ?Point
    {
        foreach ($this->_navigationPoints as $point) {
            if ($point->getId() == $id) {
                return $point;
            }
        }
        return null;
    }

	/**
	 * @param Point $navigationPoint
	 */
    public function add(Point $navigationPoint): void
    {
        if ($this->_hasChildren) {
            $navigationPoint->hasChildren(false);
            $this->_navigationPoints[] = $navigationPoint;
        }
    }

}