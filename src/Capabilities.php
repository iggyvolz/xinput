<?php

namespace iggyvolz\xinput;

use FFI\CData;

final readonly class Capabilities
{
    /** @param list<CapabilityFlag> $flags */
    private function __construct(
        public ControllerType $controllerType,
        public ControllerSubType $controllerSubType,
        public array $flags,
        public Gamepad $gamepad,
        public Vibration $vibration
    )
    {
    }

    public static function from(CData $capabilities): self
    {
        return new self(
            controllerType: ControllerType::from($capabilities->Type),
            controllerSubType: ControllerSubType::tryFrom($capabilities->SubType) ?? ControllerSubType::Gamepad,
            flags: CapabilityFlag::fromInt($capabilities->Flags),
            gamepad: Gamepad::from($capabilities->Gamepad),
            vibration: Vibration::from($capabilities->Vibration));
    }
}