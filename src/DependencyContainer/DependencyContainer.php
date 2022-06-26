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
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Ping\DependencyContainer;

use Pingframework\Ping\Annotations\Autowired;
use Pingframework\Ping\DependencyContainer\Builder\AttributeScanner\AttributeScannerResultSet;
use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DependencyContainer implements DependencyContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $resolved = [];

    public function __construct(
        private readonly AttributeScannerResultSet $rs,
        private readonly ArgumentInjector          $ai,
        private readonly array                     $definitions = [],
    ) {
        $this->resolved[ContainerInterface::class] = $this;
        $this->resolved[DependencyContainerInterface::class] = $this;
        $this->resolved[static::class] = $this;
    }

    public function getAttributeScannerResultSet(): AttributeScannerResultSet
    {
        return $this->rs;
    }

    /**
     * Push resolved service into dependency container.
     * Replaces already existing resolved service if any.
     *
     * @param string $id
     * @param mixed  $resolved
     *
     * @return static
     */
    public function set(string $id, mixed $resolved): static
    {
        $this->resolved[$id] = $resolved;
        return $this;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        if (isset($this->resolved[$id]) || isset($this->definitions[$id])) {
            return true;
        }

        if (class_exists($id)) {
            $rc = new ReflectionClass($id);
            if ($rc->isInstantiable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Makes new instance of the service.
     * Automatically resolves the arguments of the constructor.
     * It is not storing new instance of the object into resolved internal map.
     *
     * @param string $serviceClass
     * @param array  $runtime
     * @return object
     * @throws DependencyContainerException
     */
    public function make(string $serviceClass, array $runtime = []): object
    {
        try {
            $rc = new ReflectionClass($serviceClass);
            $args = [[], []];
            $constructor = $rc->getConstructor();
            if ($constructor !== null) {
                $args = $this->ai->findArguments($this, $this->rs->getVdm(), $serviceClass, $constructor->getName(), $runtime);
            }
            $service = $rc->newInstance(...$args[0], ...$args[1]);
        } catch (ReflectionException $e) {
            throw new DependencyContainerException($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($rc->getMethods() as $rm) {
            $autowire = $this->getAutowireAttribute($rm);
            if ($autowire !== null) {
                $this->call($service, $rm->getName());
            }
        }

        return $service;
    }

    private function getAutowireAttribute(ReflectionMethod $rm): ?Autowired
    {
        foreach ($rm->getAttributes(Autowired::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }

    /**
     * Calls object method with automatically resolved arguments.
     *
     * @param object $service
     * @param string $methodName
     * @param array  $runtime
     * @return mixed
     * @throws DependencyContainerException
     */
    public function call(object $service, string $methodName, array $runtime = []): mixed
    {
        try {
            $args = $this->ai->findArguments($this, $this->rs->getVdm(), $service::class, $methodName, $runtime);
        } catch (ReflectionException $e) {
            throw new DependencyContainerException($e->getMessage(), $e->getCode(), $e);
        }

        return $service->{$methodName}(...$args[0], ...$args[1]);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @template T
     *
     * @param class-string<T> $id Identifier of the entry to look for.
     *
     * @return T Entry.
     *
     * @throws ServiceResolveException
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     */
    public function get($id): mixed
    {
        if (isset($this->definitions[$id])) {
            $id = $this->definitions[$id];
        }

        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $this->resolved[$id] = $this->make($id);
        return $this->resolved[$id];
    }
}