<?php

/**
 * Ping Boot
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

namespace Pingframework\Ping\DependencyContainer\Builder\AttributeScanner;

use Attribute;
use Pingframework\Ping\Annotations\Variadic;
use Pingframework\Ping\DependencyContainer\Definition\AttributeClassDefinitionMap;
use Pingframework\Ping\DependencyContainer\Definition\VariadicDefinitionMap;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class AttributeScannerResultSet
{
    private VariadicDefinitionMap $vdm;
    private AttributeClassDefinitionMap $acdm;

    public function __construct() {
        $this->vdm = new VariadicDefinitionMap();
        $this->acdm = new AttributeClassDefinitionMap();
    }

    public function add(ReflectionClass $rc): void
    {
        foreach ($rc->getAttributes() as $attribute) {
            if ($attribute->getName() === Attribute::class) {
                continue;
            }

            $this->acdm->put($rc);

            if (is_subclass_of($attribute->getName(), Variadic::class) || $attribute->getName() === Variadic::class) {
                /** @var Variadic $ai */
                $ai = $attribute->newInstance();
                foreach ($ai->targetServices as $targetService) {
                    $this->vdm->put($targetService, $rc->getName(), $ai->priority);
                }
            }
        }
    }

    /**
     * @return VariadicDefinitionMap
     */
    public function getVdm(): VariadicDefinitionMap
    {
        return $this->vdm;
    }

    /**
     * @return AttributeClassDefinitionMap
     */
    public function getAcdm(): AttributeClassDefinitionMap
    {
        return $this->acdm;
    }
}
