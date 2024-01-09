<?php

namespace iggyvolz\xinput;

// https://learn.microsoft.com/en-us/windows/win32/xinput/xinput-and-controller-subtypes
enum ControllerSubType: int
{
    case Unknown = 0x00;
    case Gamepad = 0x01;
    case Wheel = 0x02;
    case ArcadeStick = 0x03;
    case FlightStick = 0x04;
    case DancePad = 0x05;
    case Guitar = 0x06;
    case GuitarAlternate = 0x07;
    case DrumKit = 0x08;
    case GuitarBase = 0x0B;
    case ArcadePad = 0x13;
}
