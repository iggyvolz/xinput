<?php

namespace iggyvolz\xinput;

enum Button: int
{
    use BitmapEnum;
    case DpadUp = 0;
    case DpadDown = 1;
    case DpadLeft = 2;
    case DpadRight = 3;
    case Start = 4;
    case Back = 5;
    case LeftThumb = 6;
    case RightThumb = 7;
    case LeftShoulder = 8;
    case RightShoulder = 9;
    case A = 12;
    case B = 13;
    case X = 14;
    case Y = 15;
}
