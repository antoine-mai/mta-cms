<?php namespace Admin\Services\Parser\Interfaces;

interface ParserInterface
{
    public function parse($template, $data, $return = false);
    public function parse_string($template, $data, $return = false);
    public function set_delimiters($l = '{', $r = '}');
}
