<?php

use iggyvolz\xinput\Button;
use iggyvolz\xinput\DeviceNotConnectedException;
use iggyvolz\xinput\Vibration;
use iggyvolz\xinput\WinException;
use iggyvolz\xinput\XInput;

require_once __DIR__ . "/vendor/autoload.php";
$xinput = new XInput();
while(true) {
    echo chr(27).chr(91).'H'.chr(27).chr(91).'J';   //^[H^[J

    for($i=0; $i<4; $i++) {
        try {
            $batteryInformation = $xinput->getBatteryInformation($i);
            $capabilities = $xinput->getCapabilities($i);
            $state = $xinput->getState($i);
        } catch(DeviceNotConnectedException) {
            echo "Controller $i: not connected" . PHP_EOL;
            continue;
        } catch (WinException $e) {
            echo "Unexpected Windows error " . $e->getCode() . PHP_EOL;
            exit(1);
        }
        echo "Controller $i: connected" . PHP_EOL;
        echo "Battery type: " . $batteryInformation->batteryType->name . ", level: " . $batteryInformation->batteryLevel->name . PHP_EOL;
        echo "Controller type: " . $capabilities->controllerSubType->name . PHP_EOL;
        echo "Supported buttons: " . implode(" ", array_map(fn(Button $x) => $x->name, $capabilities->gamepad->buttons)) . PHP_EOL;
        echo "Currently pressed buttons: " . implode(" ", array_map(fn(Button $x) => $x->name, $state->buttons)) . PHP_EOL;
        echo "Trigger levels: ";
        foreach(["Left" => $state->leftTrigger, "Right" => $state->rightTrigger] as $name => $level) {
            $pct = round($level / 2.55);
            echo "$name $level ($pct%";
            if($level < 30) {
                echo " - DEAD";
            }
            echo ")";
            if($name === "Left") echo ", ";
        }
        try {
            $xinput->setState($i, new Vibration($state->leftTrigger * 256, $state->rightTrigger * 256));
        } catch (WinException $e) {
            echo "Unexpected Windows error " . $e->getCode() . PHP_EOL;
            exit(1);
        }
        echo PHP_EOL;
        echo "Thumbsticks: ";
        foreach(["Left" => [$state->thumbLeftX, $state->thumbLeftY, 7849], "Right" => [$state->thumbRightX, $state->thumbRightY, 8689]] as $name => [$x, $y, $deadZone]) {
            $magnitude = sqrt($x * $x + $y * $y);
            $normalizedX = round($x / $magnitude, 2);
            $normalizedY = round($y / $magnitude, 2);
            $deg = round(180 * acos($normalizedX) / pi(), 2);
            if($y < 0) $deg = 360-$deg;
            $dead = $deadZone >= $magnitude;
            $magnitude = round($magnitude, 2);
            $pct = min(100, round($magnitude / 327.67, 2));
            echo "$name ($normalizedX,$normalizedY) $deg degrees, mag $magnitude ($pct%)";
            if($dead) echo " - DEAD";
            if($name === "Left") echo ", ";
        }
        echo PHP_EOL;
    }
    usleep(1000);
}
