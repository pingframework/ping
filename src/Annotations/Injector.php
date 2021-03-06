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

use Pingframework\Ping\DependencyContainer\Definition\VariadicDefinitionMap;
use Pingframework\Ping\DependencyContainer\DependencyContainerException;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface Injector
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
    ): mixed;
}