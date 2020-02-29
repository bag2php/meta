<?php

declare(strict_types=1);

namespace Bag2\Meta;

use Closure;

/**
 * @implements Option<null>
 */
final class None implements Option
{
    public function __construct()
    {
        // noop
    }

    public function get()
    {
        throw new \Exception("");
    }

    public function isDefined(): bool
    {
        return false;
    }

    public function isEmpty(): bool
    {
        return true;
    }

    /**
     * @template E
     * @phan-param E $else
     * @psalm-param E $else
     * @phan-return E
     * @psalm-return E
     */
    public function getOrElse($else)
    {
        return $else;
    }

    public function getOrNull()
    {
        return null;
    }

    /**
     * @psalm-template E
     * @psalm-param Closure(T): E
     * @return $this
     */
    public function map(Closure $f)
    {
        return $this;
    }
}
