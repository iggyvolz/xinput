<?php

namespace iggyvolz\xinput;

use FFI\CData;

final readonly class Vibration
{
    public function __construct(
        public int $leftMotorSpeed,
        public int $rightMotorSpeed,
    )
    {
    }

    public static function from(CData $vibration): self
    {
        return new self(
            leftMotorSpeed: $vibration->wLeftMotorSpeed,
            rightMotorSpeed: $vibration->wRightMotorSpeed
        );
    }
}