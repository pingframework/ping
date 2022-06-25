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


use Pingframework\Ping\Annotations\Service;
use JsonException;

/**
 * JSON encoder.
 *
 * @package   pingframework\ping
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(JsonEncoderInterface::class)]
class JsonEncoder implements JsonEncoderInterface
{
    /**
     * Converts PHP array into json string.
     *
     * @param array $data
     *
     * @return string
     *
     * @throws JsonException
     */
    public function marshal(array $data): string
    {
        $json = json_encode($data, JSON_THROW_ON_ERROR);

        if ($json === false) {
            throw new JsonException("Can't encode data into JSON string", 0);
        }

        return $json;
    }
}
