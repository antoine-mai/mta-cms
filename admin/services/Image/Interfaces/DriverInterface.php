<?php namespace Admin\Services\Image\Interfaces;

interface DriverInterface
{
    public function resize();
    public function crop();
    public function rotate();
    public function watermark();
}
