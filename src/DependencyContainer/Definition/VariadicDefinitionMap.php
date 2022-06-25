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

namespace Pingframework\Ping\DependencyContainer\Definition;

use FluentTraversable\Semantics\get;
use FluentTraversable\Semantics\is;
use Pingframework\Ping\Utils\Arrays\Arrays;
use Pingframework\Ping\Utils\Priority;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VariadicDefinitionMap
{
    /**
     * @var array<string, array<VariadicDefinition>>
     */
    private array $map = [];

    public function put(string $targetService, string $dependencyService, int $priority = Priority::NORMAL): void
    {
        if ($this->contains($targetService, $dependencyService)) {
            return;
        }

        $this->map[$targetService][] = new VariadicDefinition($dependencyService, $priority);
    }

    public function contains(string $targetService, string $dependencyService): bool
    {
        foreach ($this->map[$targetService] ?? [] as $definition) {
            if ($definition->dependencyService === $dependencyService) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns sorted list of variadic dependencies.
     *
     * @param string $targetService
     * @return string[]
     */
    public function get(string $targetService): array
    {
        if (!isset($this->map[$targetService])) {
            return [];
        }

        return Arrays::stream($this->map[$targetService])
            ->orderBy(get::value('priority'), 'DESC')
            ->map(get::value('dependencyService'))
            ->toArray();
    }

    public function remove(string $targetService, string $serviceToDrop): void
    {
        if (!isset($this->map[$targetService])) {
            return;
        }

        $this->map[$targetService] = Arrays::stream($this->map[$targetService])
            ->filter(is::notEq('dependencyService', $serviceToDrop))
            ->toArray();
    }
}