<?php

declare(strict_types=1);

/**
 * Helper functions for meta programming
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
namespace Bag2\Meta
{
    use ReflectionFunction;
    use ReflectionFunctionAbstract;
    use ReflectionMethod;

    function create_reflection(callable $callback): ReflectionFunctionAbstract
    {
        if (\is_array($callback)) {
            return new ReflectionMethod($callback[0], $callback[1]);
        }

        if (\is_string($callback) && \strpos($callback, '::') !== false) {
            return new ReflectionMethod($callback);
        }

        /** @var \Closure|callable-string $callback */
        return new ReflectionFunction($callback);
    }

    /**
     * @param mixed $value
     */
    function var_type($value): string
    {
        if (\is_object($value)) {
            return \get_class($value);
        }

        $type = \gettype($value);
        $canonical = [
            'boolean' => 'bool',
            'double' => 'float',
            'integer' => 'int',
            'NULL' => 'null',
        ];

        return isset($canonical[$type]) ? $canonical[$type] : $type;
    }
}
