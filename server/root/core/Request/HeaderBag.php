<?php namespace Root\Core\Request;
/**
 * HeaderBag Class
 *
 * Represents a collection of HTTP headers.
**/
class HeaderBag
{
    protected $headers = [];

    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    public function all()
    {
        return $this->headers;
    }

    public function keys()
    {
        return array_keys($this->headers);
    }

    public function replace(array $headers = [])
    {
        $this->headers = [];
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    public function add(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    public function get($key, $default = null)
    {
        $key = str_replace('_', '-', strtolower((string)$key));

        if (!array_key_exists($key, $this->headers)) {
            if (null === $default) {
                return null;
            }

            return $default;
        }

        return $this->headers[$key];
    }

    public function set($key, $values)
    {
        $key = str_replace('_', '-', strtolower((string)$key));

        $this->headers[$key] = $values;
    }

    public function has($key)
    {
        return array_key_exists(str_replace('_', '-', strtolower((string)$key)), $this->headers);
    }

    public function contains($key, $value)
    {
        $header = $this->get($key);

        if (null === $header) {
            return false;
        }

        return strpos($header, $value) !== false;
    }

    public function remove($key)
    {
        unset($this->headers[str_replace('_', '-', strtolower((string)$key))]);
    }
}
