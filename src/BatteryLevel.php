<?php

namespace iggyvolz\xinput;

enum BatteryLevel: int
{
    case Empty = 0x00;
    case Low = 0x01;
    case Medium = 0x02;
    case Full = 0x03;
}