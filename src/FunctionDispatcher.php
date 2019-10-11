<?php

declare(strict_types=1);

namespace Bag2\Meta;

use function Bag2\Meta\var_type;
use Closure;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use TypeError;

/**
 * Dynamic dispatching functions and methods
 *
 * @copyright 2019 Baguette HQ
 * @license Apache-2.0
 * @author USAMI Kenta <tadsan@zonu.me>
 */
final class FunctionDispatcher
{
    /** @var callable */
    private $callback;

    /** @var ReflectionFunctionAbstract */
    private $ref;

    public function __construct(callable $callback, ReflectionFunctionAbstract $ref)
    {
        $this->callback = $callback;
        $this->ref = $ref;
    }

    public static function fromCallable(callable $callback): self
    {
        return self::fromClosure(Closure::fromCallable($callback));
    }

    public static function fromClosure(Closure $closure): self
    {
        return new self($closure, new ReflectionFunction($closure));
    }

    /**
     * @param array<string,mixed> $values
     * @return mixed
     * @throws \ReflectionException
     */
    public function dispatch(array $values)
    {
        $params = $this->ref->getParameters();
        $args = self::_buildArgs($params, $values);

        return ($this->callback)(...$args);
    }

    /**
     * @param array<int,ReflectionParameter> $params
     * @params array<string,mixed> $values
     */
    private static function _buildArgs(array $params, array $values): array
    {
        $args = [];

        if (\count($params) != \count($values)) {
            throw new \LogicException('Given values do not match count of parameters');
        }

        foreach ($params as $n => $param) {
            $name = $param->getName();
            if (!isset($values[$name])) {
                throw new \LogicException('Given values do not match parameters of function definition');
            }

            $args[] = self::_castArg($n + 1, $param, $values[$name]);
        }

        return $args;
    }

    /**
     * Cast $value by parameter of callback
     *
     * @param int $n
     * @param ReflectionParameter $param
     * @param mixed $value
     * @return mixed
     */
    private static function _castArg(int $n, ReflectionParameter $param, $value)
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

    /**
     * @return array<string,string>
     */
    public function getParamTypes(): array
    {
        $params = [];

        foreach ($this->ref->getParameters() as $p) {
            $type = $p->hasType() ? (string)$p->getType() : 'mixed';
            $name = $p->getName();
            $params[$name] = $type;
        }

        return $params;
    }
}
