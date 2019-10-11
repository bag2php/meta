<?php

declare(strict_types=1);

namespace Bag2\Meta;

use function Bag2\Meta\create_reflection;
use LogicException;
use ReflectionFunction;
use RuntimeException;
use stdClass;
use Throwable;
use TypeError;

/**
 * Test code for FunctionDispatcher
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
final class FunctionDispatcherTest extends \Bag2\Meta\TestCase
{
    /**
     * @dataProvider forTest
     */
    public function test(array $expected, array $expected_types, callable $callback, array $params)
    {
        $ref = create_reflection($callback);
        $subject = new FunctionDispatcher($callback, $ref);

        $this->assertSame($expected, $subject->dispatch($params));
        $this->assertSame($expected_types, $subject->getParamTypes());
    }

    public function forTest()
    {
        return [
            [
                ['i' => 123],
                ['i' => 'int'],
                function (int $i) {
                    return compact('i');
                },
                ['i' => '123'],
            ],
            [
                ['s' => 'STRING'],
                ['s' => 'string'],
                function (string $s) {
                    return compact('s');
                },
                ['s' => 'STRING'],
            ],
            [
                ['s' => 'OBJECT'],
                ['s' => 'string'],
                function (string $s) {
                    return compact('s');
                },
                ['s' => new class {
                    public function __toString()
                    {
                        return 'OBJECT';
                    }
                }],
            ],
            [
                ['i' => 123, 's' => 'STRING'],
                [
                    'i' => 'int',
                    's' => 'string',
                ],
                function (int $i, string $s) {
                    return \compact('i', 's');
                },
                ['i' => '123', 's' => 'STRING'],
            ],
        ];
    }

    /**
     * @dataProvider forTest_raise_Exception
     */
    public function test_raise_Exception(array $expected, callable $callback, array $params)
    {
        $this->expectException($expected[0]);
        $this->expectExceptionMessage($expected[1]);

        $ref = create_reflection($callback);
        $subject = new FunctionDispatcher($callback, $ref);

        $subject->dispatch($params);
    }

    public function forTest_raise_Exception()
    {
        return [
            [
                [LogicException::class, 'Unexpected value'],
                function (Throwable $e) {
                },
                ['e' => new RuntimeException],
            ],
            [
                [LogicException::class, 'Given values do not match count of parameters'],
                function () {
                },
                ['a' => []],
            ],
            [
                [LogicException::class, 'Given values do not match count of parameters'],
                function ($v) {
                },
                [],
            ],
            [
                [
                    LogicException::class,
                    'Given values do not match parameters of function definition',
                ],
                function (Throwable $e) {
                },
                ['o' => new stdClass],
            ],
            [
                [
                    TypeError::class,
                    'Argument 1 passed to FunctionDispatcher must be of the type int, string given'
                ],
                function (int $n) {
                },
                ['n' => 'a'],
            ],
            [
                [
                    TypeError::class,
                    'Argument 1 passed to FunctionDispatcher must be of the type string, int given'
                ],
                function (string $s) {
                },
                ['s' => 123],
            ],
            [
                [
                    TypeError::class,
                    'Argument 1 passed to FunctionDispatcher must be of the type string, stdClass given'
                ],
                function (string $s) {
                },
                ['s' => new stdClass],
            ],
        ];
    }

    public function test_fromCallable()
    {
        $callback = function () {
        };
        $ref = new ReflectionFunction($callback);
        $expected = new FunctionDispatcher($callback, $ref);

        $actual = FunctionDispatcher::fromCallable($callback);

        $this->assertEquals($expected, $actual);
    }

    public function test_fromClosure()
    {
        $callback = function () {
        };
        $ref = new ReflectionFunction($callback);
        $expected = new FunctionDispatcher($callback, $ref);

        $actual = FunctionDispatcher::fromClosure($callback);

        $this->assertEquals($expected, $actual);
    }

    public function test_getParamTypes()
    {
        $expected = [
            'i' => 'int',
            's' => 'string',
            'a' => 'array',
            'c' => 'stdClass',
        ];
        $subject = function (int $i, string $s, array $a, \stdClass $c) {
        };
        $actual = (FunctionDispatcher::fromClosure($subject))->getParamTypes();

        $this->assertSame($expected, $actual);
    }
}
