<?php

/*
 * This file is part of Evenement.
 *
 * (c) 2014 Usman Malik <attozk@khat.pk>
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evenement;

interface EventEmitterRegexInterface
{
    const EMIT_STRATEGY_ALL = 1;
    const EMIT_STRATEGY_FIRST_MATCH = 2;

    /**
     * @param array|string $event
     * @param callable     $listener
     */
    public function on($event, callable $listener);

    /**
     * @param array|string $event
     * @param callable     $listener
     */
    public function once($event, callable $listener);

    /**
     * @param array|string $event
     * @param callable     $listener
     */
    public function removeListener($event, callable $listener);

    /**
     * @param array|string $event
     */
    public function removeAllListeners($event = null);

    /**
     * @param array|string $event
     * @param int       $strategy
     * @return array
     */
    public function listeners($event, $strategy = self::EMIT_STRATEGY_ALL);

    public function emit($event, array $arguments = [], callable $fallbackCallback = null);
}
