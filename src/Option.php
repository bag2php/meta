<?php

declare(strict_types=1);

namespace Bag2\Meta;

use Closure;

/**
 * @template T
 */
interface Option
{
    /**
     * @psalm-return T
     */
    public function get();

    public function isDefined(): bool;

    public function isEmpty(): bool;


    /**
     * @template E
     * @phan-param T|E $else
     * @psalm-param T|E $else
     * @phan-return T|E
     * @psalm-return T|E
     */
    public function getOrElse($else);

    /**
     * @phan-return ?T
     * @psalm-return ?T
     */
    public function getOrNull();

    /**
     * @psalm-template E
     * @psalm-param Closure(T): E
     * @psalm-return Option<E>
     * @return static
     */
    public function map(Closure $f);
}
