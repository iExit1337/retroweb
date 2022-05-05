<?php


namespace System\Navigation;


class Navigation
{

    /**
     * @var Navigation
     */
    private static $_instance = null;

    /**
     * @return Navigation
     */
    public static function get(): Navigation
    {
        if (self::$_instance == null) {
            self::$_instance = self::create();
        }

        return self::$_instance;
    }

    /**
     * @return Navigation
     */
    public static function create(): Navigation
    {
        return new self;
    }

    /**
     * @var Point[]
     */
    private $_navigationPoints = [];

    /**
     * @param Point $navigationPoint
     */
    public function add(Point $navigationPoint): void
    {
        $navigationPoint->hasChildren(true);
        $this->_navigationPoints[] = $navigationPoint;
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
     * @return null|Point
     */
    public function getActive(): ?Point
    {
        foreach ($this->_navigationPoints as $navigationPoint) {
            if ($navigationPoint->isActive()) {
                return $navigationPoint;
            }
        }
        return null;
    }

    /**
     * @return Point[]
     */
    public function getNavigationPoints(): array
    {
        usort($this->_navigationPoints, function (Point $a, Point $b) {
            return $b->getPosition() <=> $a->getPosition();
        });

        return $this->_navigationPoints;
    }
}