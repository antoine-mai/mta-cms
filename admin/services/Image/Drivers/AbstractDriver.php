<?php namespace Admin\Services\Image\Drivers;

use Admin\Services\Image\Image;

abstract class AbstractDriver implements \Admin\Services\Image\Interfaces\DriverInterface
{
    /** @var Image */
    public $parent;

    public function __construct(Image $parent)
    {
        $this->parent = $parent;
    }
}
