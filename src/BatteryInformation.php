<?php

namespace iggyvolz\xinput;

use FFI\CData;

final readonly class BatteryInformation
{
    public function __construct(
        public BatteryType $batteryType,
        public BatteryLevel $batteryLevel,
    )
    {
    }

    public static function from(CData $batteryInformation): self
    {
        return new self(
            batteryType: BatteryType::from($batteryInformation->BatteryType),
            batteryLevel: BatteryLevel::from($batteryInformation->BatteryLevel),
        );
    }
}