<?php

/**
 * Benchmarking against EventEmitter (original by Igor Wiedler)
 */

$loader = require __DIR__.'/../vendor/autoload.php';
require('diff.php');

$start = millitime();
$emitter = new \Evenement\EventEmitter();
$counter = 1;
$runs = 100;

for($i = 1; $i <= $runs; $i++) {
    $emitter->on('listener_'. $i, function() use(&$counter) {
        $counter++;
    });
}

for($i = 1; $i <= $runs; $i++) {
    $emitter->emit('listener_'. $i);
}
$time_EventEmitter = millitimeDiff($start);


$start = millitime();
$emitter = new \Evenement\EventEmitterRegex();
$counter = 1;
for($i = 1; $i <= $runs; $i++) {
    $emitter->on('listener_'. $i, function() use(&$counter) {
        $counter++;
    });
}

for($i = 1; $i <= $runs; $i++) {
    $emitter->emit('listener_'. $i . '$');
}
$time_EventEmitterRegex = millitimeDiff($start);


printf("Time for $runs listeners and emits\n" .
        "EventEmitter:     \t\t\t %-5d ms\n" .
        "EventEmitterRegex:\t\t\t %-5d ms\n", $time_EventEmitter, $time_EventEmitterRegex);
