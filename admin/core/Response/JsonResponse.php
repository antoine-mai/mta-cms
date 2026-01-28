<?php namespace Admin\Core\Response;
/**
 * 
**/
class JsonResponse extends Response
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $encodingOptions = 0;

    /**
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($data = null, int $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        if (null === $data) {
            $data = new \stdClass();
        }

        $this->setData($data);
    }

    /**
     * factory method for chainability
     */
    public static function create($data = null, int $status = 200, array $headers = [])
    {
        return new static($data, $status, $headers);
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this->update();
    }

    /**
     * Sets the response content.
     *
     * @return $this
     */
    public function setContent($content)
    {
        if (null !== $content) {
            return $this->setData($content);
        }

        return parent::setContent($content);
    }

    /**
     * Updates the content and headers according to the JSON data and encoding options.
     *
     * @return $this
     */
    protected function update()
    {
        $this->content = json_encode($this->data, $this->encodingOptions);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        if (!$this->headers || !isset($this->headers['Content-Type'])) {
            $this->setHeader('Content-Type', 'application/json');
        }

        return $this;
    }

    /**
     * Sets the JSON encoding options.
     *
     * @param int $encodingOptions
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions)
    {
        $this->encodingOptions = $encodingOptions;

        return $this->update();
    }
}
