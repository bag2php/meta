<?php

declare(strict_types=1);

namespace Bag2\Meta\functions;

use function Bag2\Meta\create_reflection;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Test code for Bag2\Meta\create_reflection()
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
final class CreateReflectionTest extends \Bag2\Meta\TestCase
{
    /**
     * @dataProvider forTest
     */
    public function test(string $expected, callable $callback)
    {
        $actual = create_reflection($callback);

        $this->assertInstanceOf($expected, $actual);
    }

    public function forTest()
    {
        return [
            'closure' => [
                ReflectionFunction::class,
                function () {
                },
            ],
            'callable method' => [
                ReflectionMethod::class,
                [new \DateTime, 'format'],
            ],
            'callable method class-string' => [
                ReflectionMethod::class,
                [\DateTime::class, 'createFromFormat'],
            ],
            'callable-string function' => [
                ReflectionFunction::class,
                'is_string',
            ],
            'callable-string method' => [
                ReflectionMethod::class,
                __CLASS__ . '::dummy',
            ],
        ];
    }

    public static function dummy()
    {
        // noop
    }
}
