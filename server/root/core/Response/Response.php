<?php namespace Root\Core\Response;
/**
 * Response Class
 *
 * Handles HTTP responses, headers, and output.
**/
class Response
{
    /**
     * HTTP Status Codes
     *
     * @var array
     */
    protected static $statusCodes = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable'
    ];

    /**
     * Response headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Response content
     *
     * @var string
     */
    protected $content = '';

    /**
     * HTTP status code
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Constructor
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function __construct($content = '', $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $status;
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * Prepare the response
     */
    public function prepare()
    {
        // For sub-classes to override
        return $this;
    }


    /**
     * Set the HTTP status code
     *
     * @param int $code
     * @return self
     */
    public function setStatusCode($code)
    {
        $this->statusCode = (int)$code;
        return $this;
    }

    /**
     * Get the HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set a response header
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setHeader($name, $value)
    {
        $this->headers[strtolower((string)$name)] = $value;
        return $this;
    }

    /**
     * Set the response content
     *
     * @param string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = (string)$content;
        return $this;
    }

    /**
     * Send JSON response
     *
     * @param mixed $data
     * @param int $code
     * @return void
     */
    public function json($data, $code = 200)
    {
        $this->setStatusCode($code);
        $this->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->send();
    }

    /**
     * Send the response to the client
     *
     * @return void
     */
    public function send()
    {
        if (headers_sent()) {
            echo $this->content;
            return;
        }

        // Send status header
        $statusText = isset(self::$statusCodes[$this->statusCode]) ? self::$statusCodes[$this->statusCode] : 'Unknown Status';
        header("HTTP/1.1 {$this->statusCode} {$statusText}", true, $this->statusCode);

        // Send other headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true);
        }

        echo $this->content;
        exit;
    }

    /**
     * Redirect to a URL
     *
     * @param string $url
     * @param int $code
     * @return void
     */
    public function redirect($url, $code = 302)
    {
        $this->setStatusCode($code);
        $this->setHeader('Location', $url);
        $this->send();
    }
}
