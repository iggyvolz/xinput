<?php

namespace iggyvolz\xinput;

enum BatteryType: int
{
    case Disconnected = 0x00;
    case Wired = 0x01;
    case Alkaline = 0x02;
    case NickelMetalHydride = 0x03;
    case Unknown = 0xFF;
}