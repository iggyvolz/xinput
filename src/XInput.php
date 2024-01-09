<?php

namespace iggyvolz\xinput;

use FFI;

final class XInput
{
    private readonly FFI $ffi;

    public function __construct(string $dll = "XINPUT1_4.DLL")
    {
        $this->ffi = FFI::cdef(<<<CDEF
            typedef int BOOL;
            typedef unsigned char BYTE;
            typedef short SHORT;
            typedef unsigned long DWORD;
            typedef int16_t WCHAR;
            typedef WCHAR *LPWSTR;
            typedef unsigned int UINT;
            typedef unsigned short WORD;
            typedef struct _XINPUT_BATTERY_INFORMATION {
              BYTE BatteryType;
              BYTE BatteryLevel;
            } XINPUT_BATTERY_INFORMATION, *PXINPUT_BATTERY_INFORMATION;
            typedef struct _XINPUT_GAMEPAD {
              WORD  wButtons;
              BYTE  bLeftTrigger;
              BYTE  bRightTrigger;
              SHORT sThumbLX;
              SHORT sThumbLY;
              SHORT sThumbRX;
              SHORT sThumbRY;
            } XINPUT_GAMEPAD, *PXINPUT_GAMEPAD;
            typedef struct _XINPUT_KEYSTROKE {
              WORD  VirtualKey;
              WCHAR Unicode;
              WORD  Flags;
              BYTE  UserIndex;
              BYTE  HidCode;
            } XINPUT_KEYSTROKE, *PXINPUT_KEYSTROKE;
            typedef struct _XINPUT_STATE {
              DWORD          dwPacketNumber;
              XINPUT_GAMEPAD Gamepad;
            } XINPUT_STATE, *PXINPUT_STATE;
            typedef struct _XINPUT_VIBRATION {
              WORD wLeftMotorSpeed;
              WORD wRightMotorSpeed;
            } XINPUT_VIBRATION, *PXINPUT_VIBRATION;
            typedef struct _XINPUT_CAPABILITIES {
              BYTE             Type;
              BYTE             SubType;
              WORD             Flags;
              XINPUT_GAMEPAD   Gamepad;
              XINPUT_VIBRATION Vibration;
            } XINPUT_CAPABILITIES, *PXINPUT_CAPABILITIES;
            DWORD XInputGetBatteryInformation(
              DWORD                      dwUserIndex,
              BYTE                       devType,
              XINPUT_BATTERY_INFORMATION *pBatteryInformation
            );
            DWORD XInputGetCapabilities(
              DWORD               dwUserIndex,
              DWORD               dwFlags,
              XINPUT_CAPABILITIES *pCapabilities
            );
            DWORD XInputGetKeystroke(
              DWORD             dwUserIndex,
              DWORD             dwReserved,
              PXINPUT_KEYSTROKE pKeystroke
            );
            DWORD XInputGetState(
              DWORD        dwUserIndex,
              XINPUT_STATE *pState
            );
            DWORD XInputSetState(
              DWORD            dwUserIndex,
              XINPUT_VIBRATION *pVibration
            );
        CDEF, $dll);
    }

    /**
     * @throws WinException
     */
    private static function assert(int $result): void
    {
        if($result === DeviceNotConnectedException::CODE) {
            throw new DeviceNotConnectedException();
        }
        if($result !== 0) {
            throw new WinException($result);
        }
    }

    /**
     * @throws WinException
     */
    public function getCapabilities(int $index, ?ControllerType $controllerType = null): Capabilities
    {
        $capabilities = $this->ffi->new("XINPUT_CAPABILITIES");
        self::assert($this->ffi->XInputGetCapabilities($index, $controllerType?->value ?? 0, FFI::addr($capabilities)));
        return Capabilities::from($capabilities);
    }

    /**
     * @throws WinException
     */
    public function getBatteryInformation(int $index, BatteryDeviceType $deviceType = BatteryDeviceType::Gamepad): BatteryInformation
    {
        $batteryInformation = $this->ffi->new("XINPUT_BATTERY_INFORMATION");
        self::assert($this->ffi->XInputGetBatteryInformation($index, $deviceType->value, FFI::addr($batteryInformation)));
        return BatteryInformation::from($batteryInformation);
    }

    /**
     * @var array<int,FFI\CData> array of cached states
     * Since Gamepad::from() copies in the values, we can modify the old one without worry
     * Takes advantage of State containing a packet number, which will not update if the data is not modified
     */
    private array $statesCdata = [];
    /**
     * @var array<int,Gamepad> array of cached states (Gamepad data)
     * If statesCdata[$i] is set, then states[$i] is also set and to be treated as valid as long as $statesCdata[$i] is valid
     */
    private array $states = [];

    /**
     * @throws WinException
     */
    public function getState(int $index): Gamepad
    {
        $currentPacketNumber = ($this->statesCdata[$index] ?? null)?->dwPacketNumber;
        $this->statesCdata[$index] ??= $this->ffi->new("XINPUT_STATE");
        self::assert($this->ffi->XInputGetState($index, FFI::addr($state = $this->statesCdata[$index])));
        if($state->dwPacketNumber !== $currentPacketNumber) {
            $this->states[$index] = Gamepad::from($state->Gamepad);
        }
        return $this->states[$index];
    }

    /**
     * @throws WinException
     */
    public function setState(int $index, Vibration $vibration): Vibration
    {
        $vibrationCdata = $this->ffi->new("XINPUT_VIBRATION");
        $vibrationCdata->wLeftMotorSpeed = $vibration->leftMotorSpeed;
        $vibrationCdata->wRightMotorSpeed = $vibration->rightMotorSpeed;
        self::assert($this->ffi->XInputSetState($index, FFI::addr($vibrationCdata)));
        return Vibration::from($vibrationCdata);
    }
}