<?php

namespace Bag2\Meta\Func;

use function Bag2\Meta\var_type;
use ReflectionParameter;
use TypeError;

/**
 * Default implementation to cast a value to passing parameter
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
class DefaultCaster implements ParameterCasterInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $n
     * @param ReflectionParameter $param
     * @param mixed $value
     * @return mixed
     */
    public function cast(int $n, ReflectionParameter $param, $value)
    {
        if (!$param->hasType()) {
            return $value;
        }

        $type = (string)$param->getType();

        if ($type === 'int') {
            if (!(\is_string($value) && \ctype_digit($value))) {
                $given_type = var_type($value);
                throw new TypeError("Argument {$n} passed to FunctionDispatcher must be of the type int, {$given_type} given");
            }

            return (int)$value;
        }

        if ($type === 'string') {
            if (!\is_string($value)
                && !(\is_object($value) && \method_exists($value, '__toString'))
            ) {
                $given_type = var_type($value);
                throw new TypeError("Argument {$n} passed to FunctionDispatcher must be of the type string, {$given_type} given");
            }

            return (string)$value;
        }

        throw new \LogicException('Unexpected value');
    }
}
