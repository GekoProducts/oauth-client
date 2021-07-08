<?php

namespace GekoProducts\OAuth\Exceptions;

use Throwable;
use Psr\Http\Message\ResponseInterface;

class GekoProductsServerException extends \Exception {

    public $response;

    public $data;

    public function __construct(ResponseInterface $response, array $data, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;

        $this->data = $data;

        parent::__construct($this->response->getBody(), $code, $previous);
    }

}

