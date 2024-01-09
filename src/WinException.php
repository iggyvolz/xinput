<?php

namespace iggyvolz\xinput;

use Exception;

class WinException extends Exception
{
    public function __construct(int $code)
    {
        parent::__construct(code: $code);
    }
}