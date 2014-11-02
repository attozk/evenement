<?php

/**
 * Benchmarking against EventEmitter (original by Igor Wiedler)
 */

$loader = require __DIR__.'/../vendor/autoload.php';
require('diff.php');

$start = millitime();
$emitter = new \Evenement\EventEmitter();
$counter = 1;
$listeners = 100;
$emitters = 10000;

for($i = 1; $i <= $listeners; $i++) {
    $emitter->on('listener_'. $i, function() use(&$counter) {
        $counter++;
    });
}

for($i = 1; $i <= $emitters; $i++) {
    $emitter->emit('listener_'. $i);
}
$time_EventEmitter = millitimeDiff($start);


$start = millitime();
$emitter = new \Evenement\EventEmitterRegex();
$counter = 1;
for($i = 1; $i <= $listeners; $i++) {
    $emitter->on('listener_'. $i, function() use(&$counter) {
        $counter++;
    });
}

for($i = 1; $i <= $emitters; $i++) {
    $emitter->emit('listener_'. $i . '$');
}
$time_EventEmitterRegex = millitimeDiff($start);


printf("Time for $listeners listeners and $emitters emits\n" .
        "EventEmitter:     \t\t\t %-5d ms\n" .
        "EventEmitterRegex:\t\t\t %-5d ms\n", $time_EventEmitter, $time_EventEmitterRegex);