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

use Attribute;
use FluentTraversable\Semantics\is;
use Pingframework\Ping\Utils\Arrays\Arrays;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeClassDefinitionMap
{
    /**
     * @var array<string, array<ReflectionClass>>
     */
    private array $map = [];

    public function put(ReflectionClass $rc): void
    {
        foreach ($rc->getAttributes() as $attribute) {
            if ($attribute->getName() === Attribute::class
                || (isset($this->map[$attribute->getName()]) && in_array($rc, $this->map[$attribute->getName()]))
            ) {
                continue;
            }

            $this->map[$attribute->getName()][] = $rc;
        }
    }

    /**
     * Returns list of classes for given attribute.
     *
     * @param string $attributeType
     * @param bool   $isInstanceOf
     *
     * @return ReflectionClass[]
     */
    public function get(string $attributeType, bool $isInstanceOf = false): array
    {
        if ($isInstanceOf) {
            return Arrays::stream($this->map)
                ->filter(fn(array $l, string $ac) => is_subclass_of($ac, $attributeType) || $ac === $attributeType)
                ->flatten()
                ->toArray();
        }

        return $this->map[$attributeType] ?? [];
    }

    /**
     * @return array<string, array<ReflectionClass>>
     */
    public function getAll(): array
    {
        return $this->map;
    }
}