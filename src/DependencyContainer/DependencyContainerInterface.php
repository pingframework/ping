<?php

/**
 * Ping
 *
 * MIT License
 *
 * Copyright (c) 2021 ping-framework
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace Pingframework\Ping\DependencyContainer;


use Pingframework\Ping\DependencyContainer\Definition\VariadicDefinitionMap;
use Psr\Container\ContainerInterface;

/**
 * Dependency container.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT
 */
interface DependencyContainerInterface extends ContainerInterface
{
    /**
     * Push resolved service into dependency container.
     * Replaces already existing resolved service if any.
     *
     * @param string $id
     * @param mixed  $resolved
     *
     * @return static
     */
    public function set(string $id, mixed $resolved): static;

    /**
     * Makes new instance of the service.
     * Automatically resolves the arguments of the constructor.
     * It is not storing new instance of the object into resolved internal map.
     *
     * @param string $serviceClass
     * @param array  $runtime
     * @return object
     * @throws ServiceResolveException
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     */
    public function make(string $serviceClass, array $runtime = []): object;

    /**
     * Calls object method with automatically resolved arguments.
     *
     * @param object $service
     * @param string $methodName
     * @param array  $runtime
     * @return mixed
     * @throws DependencyContainerException
     */
    public function call(object $service, string $methodName, array $runtime = []): mixed;

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
    public function get($id): mixed;

    public function getVariadicDefinitionMap(): VariadicDefinitionMap;
}
