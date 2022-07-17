<?php

/**
 * Ping
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * Json RPC://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Pingframework\Ping\Utils;

/**
 * A container object which may or may not contain a non-null value.
 * If a value is present, isPresent() will return true and get() will return the value.
 * Additional methods that depend on the presence or absence of a contained value are provided,
 * such as orElse() (return a default value if value not present)
 * and ifPresent() (execute a block of code if the value is present).
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Optional
{
    public function __construct(
        private mixed $value = null
    ) {}

    /**
     * Return true if there is a value present, otherwise false.
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->value !== null;
    }

    /**
     * If a value is present in this Optional, returns the value, otherwise throws NoSuchElementException.
     *
     * @return mixed the non-null value held by this Optional
     * @throws NoSuchElementException if there is no value present
     */
    public function get(): mixed
    {
        if (!$this->isPresent()) {
            throw new NoSuchElementException('Optional value is not present');
        }

        return $this->value;
    }

    /**
     * Return true if there is a value not present, otherwise false.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->value === null;
    }

    /**
     * Returns an empty Optional instance.
     *
     * @return static
     */
    public static function empty(): static
    {
        return new static();
    }

    /**
     * Returns an Optional describing the specified value, if non-null, otherwise returns an empty Optional.
     *
     * @param mixed $value
     * @return static
     */
    public static function of(mixed $value): static
    {
        return new static($value);
    }

    /**
     * Indicates whether some other object is "equal to" this Optional.
     *
     * @param Optional $other
     * @return bool
     */
    public function equals(Optional $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * If a value is present, invoke the specified consumer with the value, otherwise do nothing.
     *
     * @param callable $callback
     *
     * @return static
     */
    public function ifPresent(callable $callback): static
    {
        if ($this->isPresent()) {
            $callback($this->value);
        }

        return $this;
    }

    /**
     * Return the value if present, otherwise return other.
     *
     * @param mixed $other
     * @return mixed
     */
    public function orElse(mixed $other): mixed
    {
        return $this->isPresent() ? $this->value : $other;
    }

    /**
     * Return the value if present, otherwise invoke other and return the result of that invocation.
     *
     * @param callable $callback
     * @return mixed
     */
    public function orElseGet(callable $callback): mixed
    {
        return $this->isPresent() ? $this->value : $callback();
    }
}