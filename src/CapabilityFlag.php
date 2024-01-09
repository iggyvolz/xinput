<?php

namespace iggyvolz\xinput;

enum CapabilityFlag: int
{
    use BitmapEnum;
    case ForceFeedbackSupported = 0;
    case Wireless = 1;
    case VoiceSupported = 2;
    case PlugInModulesSupported = 3;
    case NoNavigation = 4;
}