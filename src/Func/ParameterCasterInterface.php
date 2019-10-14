<?php

namespace Bag2\Meta\Func;

use ReflectionParameter;

/**
 * Interface to cast a value to passing parameter
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
interface ParameterCasterInterface
{
    /**
     * Cast parameter
     *
     * @param int $n
     * @param ReflectionParameter $param
     * @param mixed $value
     * @return mixed
     */
    public function cast(int $n, ReflectionParameter $param, $value);
}
