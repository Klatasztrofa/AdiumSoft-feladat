<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Framework\MySQL;

/**
 * Egy minta controller osztály, a keretrendszer funkcióinak bemutatásához.
 */
class ExampleController
{
    public function __construct(
        private readonly string $greeting,
        private readonly MySQL $db,
    ) {
    }

    public function sayHello(HttpRequest $request): HttpResponseInterface
    {
        $name = $request->query["name"] ?? null;
        if (!is_string($name)) {
            return new JsonHttpResponse(["error" => "'name' query paraméter kötelező"], 400);
        }
        return new JsonHttpResponse(["message" => "{$this->greeting}, {$name}!"], 200);
    }

    public function getBudapestMaxTempCount(): HttpResponseInterface
    {
        $rows = $this->db->queryAssoc(
            "SELECT 
                COUNT(*) AS `count` 
            FROM 
                weather.maximum_temperatures 
            WHERE 
                city_id = :cityId
            ",
            [":cityId" => 1],
        );
        return new JsonHttpResponse($rows[0]);
    }
}
