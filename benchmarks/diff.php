<?php

/**
 * @url http://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
 * @return string
 */
function millitime()
{
    $microtime = microtime();
    $comps = explode(' ', $microtime);

    // Note: Using a string here to prevent loss of precision
    // in case of "overflow" (PHP converts it to a double)
    return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
}

function millitimeDiff($start, $now = NULL)
{
    if (!$now) {
        $now = millitime();
    }

    return ($now - $start);
}