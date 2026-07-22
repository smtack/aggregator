<?php

namespace Core;

class Response
{
    public static function abort(int $code): never
    {
        http_response_code($code);

        require view("errors/{$code}");

        exit;
    }
}