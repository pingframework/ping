<?php

/**
 * Ping
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @package   pingframework\ping
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Ping\Utils\ObjectMapper;


/**
 * Converts object into json string and back based on MapProperty attribute.
 *
 * @package   pingframework\ping
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface JsonObjectMapperInterface
{
    /**
     * Converts object to json string based on MapProperty attribute.
     *
     * @param object $object
     *
     * @return string
     *
     * @throws ObjectMapperException
     */
    public function unmap(object $object): string;

    /**
     * Converts list of objects to json string based on MapProperty attribute.
     *
     * @param array<object> $objects
     *
     * @return string
     *
     * @throws ObjectMapperException
     */
    public function unmapList(array $objects): string;

    /**
     * Maps json string to object's properties based on MapProperty attribute.
     *
     * @template T
     *
     * @param string $json JSON string.
     * @param class-string<T> $class Class name.
     *
     * @return T
     *
     * @throws ObjectMapperException
     */
    public function map(string $json, string $class): object;

    /**
     * Maps json string to list of objects based on MapProperty attribute.
     *
     * @template T
     *
     * @param string $json JSON string.
     * @param class-string<T> $class Class name.
     *
     * @return array<T>
     *
     * @throws ObjectMapperException
     */
    public function mapList(string $json, string $class): array;
}