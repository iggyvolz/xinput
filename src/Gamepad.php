<?php

namespace iggyvolz\xinput;

use FFI\CData;

final readonly class Gamepad
{
    /**
     * @param list<Button> $buttons
     */
    public function __construct(
        public array $buttons,
        public int $leftTrigger,
        public int $rightTrigger,
        public int $thumbLeftX,
        public int $thumbLeftY,
        public int $thumbRightX,
        public int $thumbRightY,
    )
    {
    }

    public static function from(CData $gamepad): self
    {
        return new self(
            buttons: Button::fromInt($gamepad->wButtons),
            leftTrigger: $gamepad->bLeftTrigger,
            rightTrigger: $gamepad->bRightTrigger,
            thumbLeftX: $gamepad->sThumbLX,
            thumbLeftY: $gamepad->sThumbLY,
            thumbRightX: $gamepad->sThumbRX,
            thumbRightY: $gamepad->sThumbRY
        );
    }
}