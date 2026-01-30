<?php namespace Root\Core;
/**
 * Output Class
 *
 * Responsible for sending final output to the browser.
**/
class Output
{
    /**
     * Final output string
     *
     * @var string
     */
    public $finalOutput = '';

    /**
     * List of custom headers
     *
     * @var array
     */
    public $headers = [];

    /**
     * List of mime types
     *
     * @var array
     */

    /**
     * Current mime type
     *
     * @var string
     */
    protected $mimeType = 'text/html';

    /**
     * Whether to compress output
     *
     * @var bool
     */
    protected $compressOutput = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = &Registry::getInstance('Config');
        $this->compressOutput = (
            ini_get('zlib.output_compression') === false
            && $config->item('compress', 'output') === true
            && extension_loaded('zlib')
        );
    }

    /**
     * Get final output
     *
     * @return	string
     */
    public function getOutput()
    {
        return $this->finalOutput;
    }

    /**
     * Set final output
     *
     * @param	string	$output
     * @return	Output
     */
    public function setOutput($output)
    {
        $this->finalOutput = $output;
        return $this;
    }

    /**
     * Append to final output
     *
     * @param	string	$output
     * @return	Output
     */
    public function appendOutput($output)
    {
        $this->finalOutput .= $output;
        return $this;
    }

    /**
     * Set HTTP header
     *
     * @param	string	$header
     * @param	bool	$replace
     * @return	Output
     */
    public function setHeader($header, $replace = true)
    {
        $this->headers[] = [$header, $replace];
        return $this;
    }

    /**
     * Set Content-Type header
     *
     * @param	string	$mimeType
     * @param	string	$charset
     * @return	Output
     */
    public function setContentType($mimeType, $charset = null)
    {
        if (strpos((string)$mimeType, '/') === false) {
            $extension = ltrim((string)$mimeType, '.');
            if (isset($this->mimes[$extension])) {
                if (is_array($mimeType)) {
                    $mimeType = current($mimeType);
                }
            }
        }

        $this->mimeType = $mimeType;
        if (empty($charset)) {
            $config = &Registry::getInstance('Config');
            $charset = $config->item('charset');
        }

        $header = 'Content-Type: ' . $mimeType . (empty($charset) ? '' : '; charset=' . $charset);
        $this->headers[] = [$header, true];
        return $this;
    }

    /**
     * Get current Content-Type
     *
     * @return	string
     */
    public function getContentType()
    {
        for ($i = 0, $c = count($this->headers); $i < $c; $i++) {
            if (sscanf((string)$this->headers[$i][0], 'Content-Type: %[^;]', $contentType) === 1) {
                return $contentType;
            }
        }
        return 'text/html';
    }

    /**
     * Set Status Header
     */
    public function setStatusHeader($code = 200, $text = '')
    {
        return $this;
    }

    /**
     * Display final output
     */
    public function display($output = '')
    {
        if ($output === '') {
            $output = &$this->finalOutput;
        }

        if ($this->compressOutput === true && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            ob_start('ob_gzhandler');
        }

        if (count($this->headers) > 0) {
            foreach ($this->headers as $header) {
                @header((string)$header[0], (bool)$header[1]);
            }
        }

        echo $output;
    }
}
