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

namespace Pingframework\Ping\Utils\Json;


use JsonException;

/**
 * JSON decoder interface.
 *
 * @package   pingframework\ping
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface JsonDecoderInterface
{
    /**
     * Converts json string into PHP array.
     * Expects that jsonString is a valid array convertable string.
     * If jsonString is null and default is passed, returns a default value or throws a JsonException otherwise.
     *
     * @param string|null $jsonString
     * @param array|null  $default
     *
     * @return array
     *
     * @throws JsonException in case when can't decode json string.
     */
    public function unmarshal(?string $jsonString, ?array $default = null): array;

    /**
     * Decodes a JSON string
     *
     * @link https://php.net/manual/en/function.json-decode.php
     *
     * This function only works with UTF-8 encoded strings.
     * PHP implements a superset of
     * JSON - it will also encode and decode scalar types and NULL. The JSON standard
     * only supports these values when they are nested inside an array or an object.
     *
     * @param string|null $jsonString The json string being decoded.
     *
     * @return mixed the value encoded in json in appropriate PHP type.
     * Values true, false and null (case-insensitive) are returned as TRUE, FALSE
     * and NULL respectively. NULL is returned if the
     * json cannot be decoded or if the encoded
     * data is deeper than the recursion limit.
     *
     * @throws JsonException
     */
    public function unmarshalMixed(?string $jsonString): mixed;
}
