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

namespace Evenement\Tests;

use Evenement\EventEmitterRegex;

class EventEmitterRegexTest extends \PHPUnit_Framework_TestCase
{
    private $emitter;

    public function setUp()
    {
        $this->emitter = new EventEmitterRegex();
    }

    public function testAddListenerWithLambda()
    {
        $this->emitter->on(['foo', 'bar'], function () {});
    }

    public function testAddListenerWithMethod()
    {
        $listener = new Listener();
        $this->emitter->on(['foo', 'bar'], [$listener, 'onFoo']);
    }

    public function testAddListenerWithStaticMethod()
    {
        $this->emitter->on(['foo', 'bar'], ['Evenement\Tests\Listener', 'onBar']);
    }

    public function testAddListenerWithInvalidListener()
    {
        try {
            $this->emitter->on(['foo', 'bar'], 'not a callable');
            $this->fail();
        } catch (\Exception $e) {
        }
    }

    public function testOnce()
    {
        $listenerCalled = 0;

        $this->emitter->once(['foo', 'bar'], function () use (&$listenerCalled) {
            $listenerCalled++;
        });

        $this->assertSame(0, $listenerCalled);

        $this->emitter->emit('foo');

        $this->assertSame(1, $listenerCalled);

        $this->emitter->emit('foo');

        $this->assertSame(1, $listenerCalled);

        $this->emitter->emit('bar');

        $this->assertSame(2, $listenerCalled);

        $this->emitter->emit('bar');

        $this->assertSame(2, $listenerCalled);
    }

    /*public function testOnceRegex()
    {
        $listenerCalled = 0;

        $this->emitter->once(['foo\d+', 'bar[A-Z]'], function () use (&$listenerCalled) {
            $listenerCalled++;
        });

        print_r(array_keys($this->emitter->listeners));
        die;

        $this->assertSame(0, $listenerCalled);

        $this->emitter->emit('foo');
        $this->assertSame(0, $listenerCalled);

        $this->emitter->emit('foo5');
        $this->assertSame(1, $listenerCalled);

        $this->emitter->emit('foo7');
        $this->assertSame(1, $listenerCalled);

        $this->emitter->emit('bar');
        $this->assertSame(1, $listenerCalled);

        $this->emitter->emit('bar');
        $this->assertSame(1, $listenerCalled);
    }*/

    public function testOnceWithArguments()
    {
        $capturedArgs = [];

        $this->emitter->once(['foo', 'bar'], function ($a, $b) use (&$capturedArgs) {
            $capturedArgs = array($a, $b);
        });

        $this->emitter->emit('foo', array('a', 'b'));

        $this->assertSame(array('a', 'b'), $capturedArgs);

        $this->emitter->emit('bar', array('a', 'b'));

        $this->assertSame(array('a', 'b'), $capturedArgs);
    }

    public function testOnWithTwoArguments()
    {
        $capturedArgs = [];

        $this->emitter->on(['foo\d+', 'bar[A-Z]'], function ($a, $b) use (&$capturedArgs) {
            $capturedArgs = array($a, $b);
        });

        $this->emitter->emit('foo', array('a', 'b'));
        $this->assertSame(array(), $capturedArgs);

        $this->emitter->emit('fooA', array('a', 'b'));
        $this->assertSame(array(), $capturedArgs);

        $this->emitter->emit('foo4', array('a', 'b'));
        $this->assertSame(array('a', 'b'), $capturedArgs);

        $this->emitter->emit('foo45', array('c', 'd'));
        $this->assertSame(array('c', 'd'), $capturedArgs);

        $capturedArgs = array();
        $this->emitter->emit('bar', array('a', 'b'));
        $this->assertSame(array(), $capturedArgs);

        $this->emitter->emit('bar5', array('a', 'b'));
        $this->assertSame(array(), $capturedArgs);

        $this->emitter->emit('barA', array('e', 'f'));
        $this->assertSame(array('e', 'f'), $capturedArgs);

        $this->emitter->emit('barZ', array('g', 'h'));
        $this->assertSame(array('g', 'h'), $capturedArgs);

        $this->emitter->emit('foo45', array('i', 'j'));
        $this->assertSame(array('i', 'j'), $capturedArgs);
    }

    public function testEmitWithoutArguments()
    {
        $listenerCalled = false;

        $this->emitter->on(['foo', 'bar'], function () use (&$listenerCalled) {
            $listenerCalled = true;
        });

        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('foo');
        $this->assertSame(true, $listenerCalled);

        $listenerCalled = false;
        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('bar');
        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitRegexWithoutArguments()
    {
        $listenerCalled = false;

        $this->emitter->on(['foo\d{1}$', '^bar\w+'], function () use (&$listenerCalled) {
            $listenerCalled = true;
        });

        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('foo');
        $this->assertSame(false, $listenerCalled);

        $this->emitter->emit('foo1');
        $this->assertSame(true, $listenerCalled);

        $listenerCalled = false;
        $this->emitter->emit('foo25');
        $this->assertSame(false, $listenerCalled);

        $this->emitter->emit('hello_foo9');
        $this->assertSame(true, $listenerCalled);


        $listenerCalled = false;
        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('bar');
        $this->assertSame(false, $listenerCalled);

        $this->emitter->emit('barABC');
        $this->assertSame(true, $listenerCalled);

        $listenerCalled = false;
        $this->emitter->emit('barABC548');
        $this->assertSame(true, $listenerCalled);

        $listenerCalled = false;
        $this->emitter->emit('Hello_barABC548');
        $this->assertSame(false, $listenerCalled);
    }

    public function testEmitRegexAllMatchesWithoutArguments()
    {
        $listenerCalled = 0;

        $this->emitter->on(['^foo\d{1}$', 'foo\w+'], function () use (&$listenerCalled) {
            $listenerCalled++;
        });

        $this->assertSame(0, $listenerCalled);
        $this->emitter->emit('foo');
        $this->assertSame(0, $listenerCalled);

        $this->emitter->emit('foo1');
        $this->assertSame(2, $listenerCalled);

        $this->emitter->emit('hello_foo1');
        $this->assertSame(3, $listenerCalled);
    }

    public function testEmitRegexFirstMatchWithoutArguments()
    {
        $listenerCalled = 0;

        $this->emitter->on(['^foo\d{1}$', 'foo\w+'], function () use (&$listenerCalled) {
            $listenerCalled++;
        });

        $this->assertSame(0, $listenerCalled);
        $this->emitter->emitFirstMatch('foo');
        $this->assertSame(0, $listenerCalled);

        $this->emitter->emitFirstMatch('foo1');
        $this->assertSame(1, $listenerCalled);

        $this->emitter->emitFirstMatch('hello_foo1');
        $this->assertSame(2, $listenerCalled);
    }

    public function testEmitWithOneArgument()
    {
        $test = $this;

        $listenerCalled = false;

        $this->emitter->on(['joo', 'foo'], function ($value) use (&$listenerCalled, $test) {
            $listenerCalled = true;

            $test->assertSame('bar', $value);
        });

        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('foo', ['bar']);
        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitWithTwoArguments()
    {
        $test = $this;

        $listenerCalled = false;

        $this->emitter->on(['foo', 'joo'], function ($arg1, $arg2) use (&$listenerCalled, $test) {
            $listenerCalled = true;

            $test->assertSame('bar', $arg1);
            $test->assertSame('baz', $arg2);
        });

        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('foo', ['bar', 'baz']);
        $this->assertSame(true, $listenerCalled);

        $listenerCalled = false;
        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('joo', ['bar', 'baz']);
        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitWithNoListeners()
    {
        $this->emitter->emit(['foo', 'joo']);
        $this->emitter->emit(['foo', 'joo'], ['bar']);
        $this->emitter->emit(['foo', 'joo'], ['bar', 'baz']);
    }

    public function testEmitWithTwoListeners()
    {
        $listenersCalled = 0;

        $this->emitter->on(['foo', 'bar'], function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->on(['joo', 'baz'], function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        $this->assertSame(1, $listenersCalled);

        $this->emitter->emit('bar');
        $this->assertSame(2, $listenersCalled);

        $this->emitter->emit('paz');
        $this->assertSame(2, $listenersCalled);

        $this->emitter->emit('joo');
        $this->assertSame(3, $listenersCalled);

        $this->emitter->emit('baz');
        $this->assertSame(4, $listenersCalled);
    }

    public function testEmitRegexWithTwoListeners()
    {
        $listenersCalled = 0;

        $this->emitter->on(['foo\w+', 'foos'], function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->on('foo\w+', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->on('foos', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('foos');
        $this->assertSame(4, $listenersCalled);

        $this->emitter->emit('bar');
        $this->assertSame(4, $listenersCalled);

        $this->emitter->emit('foos');
        $this->assertSame(8, $listenersCalled);
    }

    public function testEmitRegexFirstMatchWithTwoListeners()
    {
        $listenersCalled = 0;

        $this->emitter->on(['foo\w+', 'foos'], function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->on('foo\w+', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->on('foos', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emitFirstMatch('foos');
        $this->assertSame(1, $listenersCalled);

        $this->emitter->emit('bar');
        $this->assertSame(1, $listenersCalled);

        $this->emitter->emitFirstMatch('fooD');
        $this->assertSame(2, $listenersCalled);
    }

    public function testRemoveListenerMatching()
    {
        $listenersCalled = 0;

        $listener = function () use (&$listenersCalled) {
            $listenersCalled++;
        };

        $this->emitter->on(['foo', 'bar'], $listener);
        $this->emitter->removeListener('foo', $listener);

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('foo');
        $this->assertSame(0, $listenersCalled);

        $this->emitter->emit('bar');
        $this->assertSame(1, $listenersCalled);

        $this->emitter->removeListener('bar', $listener);
        $this->assertSame(1, $listenersCalled);
        $this->emitter->emit('bar');
        $this->assertSame(1, $listenersCalled);
    }

    public function testRemoveListenerRegexMatching()
    {
        $listenersCalled = 0;

        $listener = function () use (&$listenersCalled) {
            $listenersCalled++;
        };

        $this->emitter->on(['foo\d{1}', 'baa\w+'], $listener);

        $this->emitter->removeListener('foo', $listener);

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('foo5');
        $this->assertSame(1, $listenersCalled);

        $this->emitter->removeListener('foo1', $listener);
        $this->emitter->emit('foo5');
        $this->assertSame(1, $listenersCalled);

        $listenersCalled = 0;
        $this->emitter->emit('baaZ');
        $this->assertSame(1, $listenersCalled);

        $listenersCalled = 0;
        $this->emitter->emit('foo1');
        $this->assertSame(0, $listenersCalled);

        $this->emitter->removeListener('baaA', $listener);
        $listenersCalled = 0;
        $this->emitter->emit('baaZ');
        $this->assertSame(0, $listenersCalled);
    }

    public function testRemoveListenerNotMatching()
    {
        $listenersCalled = 0;

        $listener = function () use (&$listenersCalled) {
            $listenersCalled++;
        };

        $this->emitter->on('foo\d+', $listener);
        $this->emitter->removeListener('foo', $listener);

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('foo1');
        $this->assertSame(1, $listenersCalled);
    }


    public function testRemoveAllListenersMatching()
    {
        $listenersCalled = 0;

        $this->emitter->on('foo[A-Z]', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->removeAllListeners('fooZ');

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('fooA');
        $this->assertSame(0, $listenersCalled);
    }

    public function testRemoveAllListenersNotMatching()
    {
        $listenersCalled = 0;

        $this->emitter->on('foo\d', function () use (&$listenersCalled) {
            $listenersCalled++;
        });

        $this->emitter->removeAllListeners('bar[A-Z]');

        $this->assertSame(0, $listenersCalled);
        $this->emitter->emit('foo1');
        $this->assertSame(1, $listenersCalled);
    }
}
