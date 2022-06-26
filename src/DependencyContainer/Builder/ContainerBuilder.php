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

namespace Pingframework\Ping\DependencyContainer\Builder;

use Pingframework\Ping\Annotations\Autowired;
use Pingframework\Ping\Annotations\MethodRegistrar;
use Pingframework\Ping\Annotations\Service;
use Pingframework\Ping\DependencyContainer\ArgumentInjector;
use Pingframework\Ping\DependencyContainer\Builder\AttributeScanner\AttributeScanner;
use Pingframework\Ping\DependencyContainer\DependencyContainer;
use Pingframework\Ping\DependencyContainer\DependencyContainerException;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use Pingframework\Ping\Utils\Arrays\Arrays;
use ReflectionAttribute;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ContainerBuilder
{
    /**
     * @throws DependencyContainerException
     */
    public static function build(array $namespaces, ?string $excludeRegexp = null): DependencyContainerInterface
    {
        if (!in_array('Pingframework', $namespaces)) {
            $namespaces[] = 'Pingframework';
        }

        $scanner = new AttributeScanner();
        $rs = $scanner->scan($namespaces, $excludeRegexp);
        $definitions = [];
        $methodRegistrars = [];

        foreach ($rs->getAcdm()->get(Service::class, true) as $rc) {
            $definitions[$rc->getName()] = $rc->getName();

            // register aliases
            foreach ($rc->getAttributes(Service::class, ReflectionAttribute::IS_INSTANCEOF) as $ra) {
                /** @var Service $s */
                $s = $ra->newInstance();
                foreach ($s->aliases as $alias) {
                    $definitions[$alias] = $rc->getName();
                }
            }

            // find all methods registrars
            foreach ($rc->getMethods() as $rm) {
                foreach ($rm->getAttributes(MethodRegistrar::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    $methodRegistrars[] = [$attribute->newInstance(), $rc, $rm];
                }
            }
        }

        // set up service definitions
        $c = new DependencyContainer(
            $rs,
            new ArgumentInjector(),
            $definitions,
        );

        // earliest DC configuration
        Arrays::stream(
            $rs->getAcdm()->get(
                \Pingframework\Ping\Annotations\DependencyContainerConfigurator::class,
                true
            )
        )->each(function (ReflectionClass $rc) use ($c): void {
            if (!$rc->isSubclassOf(DependencyContainerConfigurator::class)) {
                throw new DependencyContainerException(
                    sprintf(
                        "Dependency container configurator %s must implement interface DependencyContainerConfigurator",
                        $rc->getName()
                    )
                );
            }

            $rc->getName()::configureDependencyContainer($c);
        });

        // resolve autowire services
        Arrays::stream(
            $rs->getAcdm()->get(
                Autowired::class,
                true
            )
        )->each(function (ReflectionClass $rc) use ($c): void {
            $c->get($rc->getName());
        });

        foreach ($methodRegistrars as $methodRegistrarRow) {
            $methodRegistrarRow[0]->registerMethod($c, $methodRegistrarRow[1], $methodRegistrarRow[2]);
        }

        return $c;
    }
}