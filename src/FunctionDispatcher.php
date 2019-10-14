<?php

declare(strict_types=1);

namespace Bag2\Meta;

use Bag2\Meta\Func\ParameterCasterInterface;
use Closure;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;

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

    /** @var ParameterCasterInterface */
    private $param_caster;

    public function __construct(
        callable $callback,
        ReflectionFunctionAbstract $ref,
        ParameterCasterInterface $param_caster = null
    ) {
        $this->callback = $callback;
        $this->ref = $ref;
        $this->param_caster = $param_caster ?? new Func\DefaultCaster;
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
        $args = $this->buildArgs($params, $values);

        return ($this->callback)(...$args);
    }

    /**
     * @param array<int,ReflectionParameter> $params
     * @params array<string,mixed> $values
     */
    private function buildArgs(array $params, array $values): array
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

            $args[] = $this->param_caster->cast($n + 1, $param, $values[$name]);
        }

        return $args;
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
