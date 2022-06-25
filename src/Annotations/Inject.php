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

namespace Pingframework\Ping\Annotations;

use Attribute;
use Pingframework\Ping\DependencyContainer\Definition\VariadicDefinitionMap;
use Pingframework\Ping\DependencyContainer\DependencyContainerException;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use Pingframework\Ping\Utils\Arrays\Arrays;
use Pingframework\Ping\Utils\Strings\Strings;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Inject implements Injector
{
    public function __construct(
        public readonly ?string $service = null
    ) {}

    /**
     * Returns value to be injected.
     *
     * @param DependencyContainerInterface           $c
     * @param VariadicDefinitionMap                  $vdm
     * @param ReflectionClass                        $rc
     * @param ReflectionMethod|null                  $rm
     * @param ReflectionParameter|ReflectionProperty $rp
     * @param array                                  $runtime
     *
     * @return mixed
     *
     * @throws DependencyContainerException
     */
    public function inject(
        DependencyContainerInterface           $c,
        VariadicDefinitionMap                  $vdm,
        ReflectionClass                        $rc,
        ?ReflectionMethod                      $rm,
        ReflectionParameter|ReflectionProperty $rp,
        array                                  $runtime
    ): mixed {
        $id = $this->findId($rm, $rp);

        if ($rp instanceof ReflectionParameter && $rp->isVariadic() && $rm->isConstructor()) {
            return Arrays::stream($vdm->get($rc->getName()))
                ->map(fn(string $id): object => $c->get($id))
                ->toArray();
        }

        if (isset($runtime[$id])) {
            return $runtime[$id];
        }

        if ($c->has($id)) {
            return $c->get($id);
        }

        $isOptional = $rp instanceof ReflectionParameter ? $rp->isOptional() : $rp->hasDefaultValue();

        if ($isOptional) {
            return $rp->getDefaultValue();
        }

        $this->throw($rm, $rp);
    }

    /**
     * @throws DependencyContainerException
     */
    protected function findId(
        ?ReflectionMethod                      $rm,
        ReflectionParameter|ReflectionProperty $rp
    ): string
    {
        if ($this->service !== null) {
            return $this->service;
        }

        $rt = $rp->getType();

        if ($rp instanceof ReflectionParameter && $rp->isVariadic() && !$rm->isConstructor()) {
            $this->throw($rm, $rp);
        }

        if ($rt->isBuiltin()) {
            return Strings::camelCaseToUnderscore($rp->getName());
        }

        if ($rt instanceof ReflectionNamedType) {
            return $rt->getName();
        }

        $this->throw($rm, $rp);
    }

    /**
     * @throws DependencyContainerException
     */
    private function throw(
        ?ReflectionMethod                      $rm,
        ReflectionParameter|ReflectionProperty $rp
    ): void
    {
        if ($rp instanceof ReflectionParameter) {
            throw new DependencyContainerException(
                sprintf(
                    "Failed to inject method argument %s into %s::%s",
                    $rp->getName(),
                    $rp->getDeclaringClass()->getName(),
                    $rm->getName(),
                )
            );
        }

        throw new DependencyContainerException(
            sprintf(
                "Failed to inject class property %s::%s",
                $rp->getDeclaringClass()->getName(),
                $rp->getName(),
            )
        );
    }
}