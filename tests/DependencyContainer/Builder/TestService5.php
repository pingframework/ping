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

namespace Pingframework\Ping\Tests\DependencyContainer\Builder;

use Pingframework\Ping\Annotations\DependencyContainerConfigurator;
use Pingframework\Ping\Annotations\Inject;
use Pingframework\Ping\Annotations\Variadic;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
#[Variadic([TestService6::class])]
#[DependencyContainerConfigurator]
class TestService5 implements \Pingframework\Ping\DependencyContainer\Builder\DependencyContainerConfigurator
{
    #[Inject]
    public readonly TestService1 $testService1;

    public function __construct(
        #[Inject('ts7')]
        public readonly TestService7 $testService7,
    ) {}

    /**
     * Calls by container builder right after container is built.
     * NOTE! The class must be tagged by DependencyContainerConfigurator attribute.
     *
     * @param DependencyContainerInterface $c
     * @return void
     */
    public static function configureDependencyContainer(DependencyContainerInterface $c): void
    {
        $c->set('ts7', new TestService7());
    }
}