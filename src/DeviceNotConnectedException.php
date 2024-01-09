<?php

namespace iggyvolz\xinput;

final class DeviceNotConnectedException extends WinException
{
    public const int CODE = 1167;
    public function __construct()
    {
        parent::__construct(self::CODE);
    }
}