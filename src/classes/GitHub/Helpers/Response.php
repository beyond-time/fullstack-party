<?php

namespace GitHub\Helpers;

class Response
{
    static function getData(\Psr\Http\Message\ResponseInterface $response)
    {
        return json_decode((string)$response->getBody());
    }

}