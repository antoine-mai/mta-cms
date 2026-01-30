<?php namespace Root\Core\Response;

class JsonResponse extends Response
{
    protected $data;

    public function __construct($data = null, int $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);


        if (null !== $data) {
            $this->setData($data);
        }
    }

    public function setData($data)
    {
        $this->data = $data;
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->setHeader('Content-Type', 'application/json');
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
