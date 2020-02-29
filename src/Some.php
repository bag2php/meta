<?php

declare(strict_types=1);

namespace Bag2\Meta;

use Closure;

/**
 * @implements Option<T>
 * @template T
 */
final class Some implements Option
{
    /**
     * @phan-var T
     * @psalm-var T
     */
    private $value;

    /**
     * @param mixed $value
     * @phan-param T $value
     * @psalm-param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isDefined(): bool
    {
        return true;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @psalm-return T
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @template E
     * @param mixed $else
     * @phan-param E $else (@phan-unused-param)
     * @psalm-param E $else
     * @phan-return T
     * @psalm-return T
     * @phan-suppress PhanTemplateTypeNotUsedInFunctionReturn
     */
    public function getOrElse($else)
    {
        return $this->value;
    }

    /**
     * @phan-return T
     * @psalm-return T
     */
    public function getOrNull()
    {
        return $this->value;
    }


    /**
     * @psalm-template E
     * @psalm-param Closure(T): E
     * @psalm-return Some<E>
     * @return Some
     */
    public function map(Closure $f)
    {
        return new Some($f($this->value));
    }
}
