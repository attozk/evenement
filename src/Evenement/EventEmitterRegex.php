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

class EventEmitterRegex extends EventEmitter implements EventEmitterRegexInterface
{
    /**
     * {@inheritdoc}
     */
    public function on($event, callable $listener)
    {
        if (is_array($event)) {
            foreach ($event as $ev) {
                parent::on($ev, $listener);
            }
        } else {
            parent::on($event, $listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function once($event, callable $listener)
    {
        if (is_array($event)) {
            foreach ($event as $ev) {
                $this->_once($ev, $listener);
            }
        } else {
            $this->_once($event, $listener);
        }
    }

    protected function _once($event, callable $listener)
    {
        $onceListener = function () use (&$onceListener, $event, $listener) {
            parent::removeListener($event, $onceListener);

            call_user_func_array($listener, func_get_args());
        };

        $this->on($event, $onceListener);
    }

     /**
      * {@inheritdoc}
      */
    public function removeListener($event, callable $listener)
    {
        if (is_array($event)) {
            foreach ($event as $ev) {
                $this->_removeListener($ev, $listener);
            }
        } else {
            $this->_removeListener($event, $listener);
        }
    }

    protected function _removeListener($event, callable $listener)
    {
        foreach ($this->listeners as $ev => $_listeners) {
            if (preg_match('%' . $ev . '%i', $event)) {
                parent::removeListener($ev, $listener);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllListeners($event = NULL)
    {
        if ($event === NULL) {
            $this->listeners = [];
        }
        else if (is_array($event)) {
            foreach ($event as $ev) {
                $this->_removeAllListeners($ev);
            }
        } else {
            $this->_removeAllListeners($event);
        }
    }

    protected function _removeAllListeners($event = NULL)
    {
        foreach ($this->listeners as $ev => $_listeners) {
            if (preg_match('%' . $ev . '%i', $event)) {
                unset($this->listeners[$ev]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function listeners($event, $strategy = self::EMIT_STRATEGY_ALL)
    {
        $listeners = [];

        if (is_array($event)) {
            foreach ($event as $ev) {
                $_listeners = $this->_listeners($ev, $strategy);

                if (!empty($_listeners)) {
                    $listeners[] += $_listeners;

                    if ($strategy == self::EMIT_STRATEGY_FIRST_MATCH)
                        break;
                }
            }
        } else {
            $listeners = $this->_listeners($event, $strategy);
        }

        return $listeners;
    }

    protected function _listeners($event, $strategy = self::EMIT_STRATEGY_ALL)
    {
        $listeners = [];

        foreach ($this->listeners as $ev => $_listeners) {

            if ($strategy == self::EMIT_STRATEGY_FIRST_MATCH && !empty($listener)) {
                break;
            }

            if (preg_match('%' . $ev . '%i', $event)) {

                foreach ($_listeners as $listener) {
                    $listeners[] = $listener;

                    if ($strategy == self::EMIT_STRATEGY_FIRST_MATCH) {
                        break;
                    }
                }
            }
        }

        return $listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function emitFirstMatch($event, array $arguments = [], callable $fallbackCallback = null)
    {
        $listeners = $this->listeners($event, self::EMIT_STRATEGY_FIRST_MATCH);
        $this->_emit($listeners, $arguments, $fallbackCallback);
    }

    /**
     * {@inheritdoc}
     */
    public function emit($event, array $arguments = [], callable $fallbackCallback = null)
    {
        $listeners = $this->listeners($event);
        $this->_emit($listeners, $arguments, $fallbackCallback);
    }

    protected function _emit($listeners, array $arguments = [], callable $fallbackCallback = null)
    {
        if (!empty($listeners)) {
            foreach ($listeners as $listener) {
                call_user_func_array($listener, $arguments);
            }
        }
        else if ($fallbackCallback) {
            $fallbackCallback();
        }
    }
}