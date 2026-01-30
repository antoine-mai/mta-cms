<?php namespace Root\Services\Download\Interfaces;

interface DownloadInterface
{
    public function force($filename = '', $data = '', $set_mime = false);
}
