<?php namespace Root\Core\Response;

class RedirectResponse extends Response
{
    protected $targetUrl;

    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);
    }

    public function setTargetUrl(string $url)
    {
        $this->targetUrl = $url;
        $this->setHeader('Location', $url);
        return $this;
    }

    public function getTargetUrl()
    {
        return $this->targetUrl;
    }
}
