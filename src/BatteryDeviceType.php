<?php

namespace iggyvolz\xinput;

enum BatteryDeviceType: int
{
    case Gamepad = 0x00;
    case Headset = 0x01;
}