<?php

declare(strict_types=1);

namespace Bag2\Meta\Func;

use function Bag2\Meta\create_reflection;
use Closure;
use LogicException;
use ReflectionFunction;
use ReflectionParameter;
use RuntimeException;
use stdClass;
use Throwable;
use TypeError;

/**
 * Test code for Func\DefaultCaster
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
final class DefaultCasterTest extends \Bag2\Meta\TestCase
{
    /**
     * @dataProvider forTest
     */
    public function test($expected, Closure $callback, $input)
    {
        $ref = new ReflectionFunction($callback);
        $subject = new DefaultCaster;
        $actual = $subject->cast(1, $ref->getParameters()[0], $input);

        $this->assertSame($expected, $actual);
    }

    public function forTest()
    {
        return [
            [
                123,
                function (int $n) {
                    return $n;
                },
                '123',
            ],
            [
                'STRING',
                function (string $s) {
                    return $s;
                },
                'STRING',
            ],
            [
                'OBJECT',
                function (string $s) {
                    return $s;
                },
                new class {
                    public function __toString()
                    {
                        return 'OBJECT';
                    }
                },
            ],
        ];
    }

    /**
     * @dataProvider forTest_raise_Exception
     */
    public function test_raise_Exception(array $expected, Closure $callback, $input)
    {
        $this->expectException($expected[0]);
        $this->expectExceptionMessage($expected[1]);

        $subject = new DefaultCaster;
        $ref = new ReflectionParameter($callback, 0);
        $subject->cast(1, $ref, $input);
    }

    public function forTest_raise_Exception()
    {
        return [
            [
                [
                    TypeError::class,
                    'Argument 1 passed to FunctionDispatcher must be of the type int, string given'
                ],
                function (int $n) {
                },
                'a',
            ],
            [
                [
                    TypeError::class,
                    'Argument 1 passed to FunctionDispatcher must be of the type string, int given'
                ],
                function (string $s) {
                },
                123,
            ],
            [
                [
                    TypeError::class,
                    'Argument 1 passed to FunctionDispatcher must be of the type string, stdClass given'
                ],
                function (string $s) {
                },
                new stdClass,
            ],
        ];
    }
}
