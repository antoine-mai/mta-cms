<?php namespace Root\Services\Image\Drivers;

use Root\Services\Image\Image;

abstract class AbstractDriver implements \Root\Services\Image\Interfaces\DriverInterface
{
    /** @var Image */
    public $parent;

    public function __construct(Image $parent)
    {
        $this->parent = $parent;
    }
}
