<?php namespace Root\Core;
/**
 * URI Class
 *
 * Parses URIs and determines routing.
**/
class Uri
{
    /**
     * List of URI segments
     *
     * @var array
     */
    public $segments = [];

    /**
     * Request object
     *
     * @var \Root\Core\Request\Request
     */
    protected $request;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->request = &Registry::getInstance('Request');
        $this->_parseSegments();
    }

    /**
     * Parse URI segments from Request
     */
    protected function _parseSegments()
    {
        $path = $this->request->getPathInfo();
        $path = trim($path, '/');
        
        if ($path !== '') {
            foreach (explode('/', $path) as $val) {
                // Filter segments if needed
                $val = trim($val);
                if ($val !== '') {
                    $this->segments[] = $val;
                }
            }
        }
        
        // Add a 0 index for convenience if you want 1-based indexing
        array_unshift($this->segments, null);
        unset($this->segments[0]);
    }

    /**
     * Fetch a URI segment
     *
     * @param	int	$n		Index
     * @param	mixed	$noResult	What to return if segment not found
     * @return	mixed
     */
    public function segment($n, $noResult = null)
    {
        return isset($this->segments[$n]) ? $this->segments[$n] : $noResult;
    }

    /**
     * Returns the segment array
     *
     * @return	array
     */
    public function segmentArray()
    {
        return $this->segments;
    }

    /**
     * Alias for segmentArray for legacy support
     */
    public function segment_array()
    {
        return $this->segmentArray();
    }

    /**
     * Fetch URI string
     *
     * @return	string
     */
    public function uriString()
    {
        return implode('/', $this->segments);
    }

    /**
     * Alias for uriString for legacy support
     */
    public function uri_string()
    {
        return $this->uriString();
    }
}
