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

namespace Pingframework\Ping\DependencyContainer;

use Pingframework\Ping\Annotations\Inject;
use Pingframework\Ping\Annotations\Injector;
use Pingframework\Ping\DependencyContainer\Definition\VariadicDefinitionMap;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ArgumentInjector
{
    /**
     * @throws ReflectionException
     * @throws DependencyContainerException
     */
    public function findArguments(
        DependencyContainerInterface $c,
        VariadicDefinitionMap        $vdm,
        string                       $class,
        string                       $method,
        array                        $runtime = []
    ): array {
        $rc = new ReflectionClass($class);
        $rm = $rc->getMethod($method);

        $args = [];
        $variadic = [];

        foreach ($rm->getParameters() as $rp) {
            if ($rp->isVariadic()) {
                $variadic = $this->getInjector($rp)->inject($c, $vdm, $rc, $rm, $rp, $runtime);
            } else {
                $args[$rp->getName()] = $this->getInjector($rp)->inject($c, $vdm, $rc, $rm, $rp, $runtime);
            }
        }

        return [$args, $variadic];
    }

    private function getInjector(ReflectionParameter $rp): Injector
    {
        foreach ($rp->getAttributes(Injector::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->newInstance();
        }

        return new Inject();
    }
}