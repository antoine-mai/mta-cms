<?php namespace Admin\Core\Response;
/**
 * 
**/
class RedirectResponse extends Response
{
    /**
     * @var string
     */
    protected $targetUrl;

    /**
     * @param string $url     The URL to redirect to
     * @param int    $status  The response status code
     * @param array  $headers An array of response headers
     */
    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);

        if (!$this->isRedirection()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }
    }

    /**
     * Returns the target URL.
     *
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * Sets the redirection target.
     *
     * @param string $url The URL to redirect to
     *
     * @return $this
     */
    public function setTargetUrl(string $url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;

        $this->setContent(
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=\'%1$s\'" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        $this->setHeader('Location', $url);

        return $this;
    }
}
