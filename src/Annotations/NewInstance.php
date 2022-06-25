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
class NewInstance implements Injector
{
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
        $rt = $rp->getType();

        if (!$rt instanceof ReflectionNamedType || $rt->isBuiltin()) {
            $this->throw($rm, $rp);
        }

        return $c->make($rt->getName(), $runtime);
    }

    /**
     * @throws DependencyContainerException
     */
    private function throw(
        ?ReflectionMethod                      $rm,
        ReflectionParameter|ReflectionProperty $rp
    ): void {
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