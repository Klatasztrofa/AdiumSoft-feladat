<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Framework\MySQL;

class WeatherController
{
    public function __construct(
        private readonly MySQL $db,
    ) {
    }

    public function getCities(): HttpResponseInterface
    {
        $rows = $this->db->queryAssoc(
            "SELECT
                id AS `id`,
                name AS `name`,
                region AS `region`
            FROM
                weather.cities
            ORDER BY
                name, id",
        );

        return new JsonHttpResponse(['rows' => $rows]);
    }

    public function getRegions(): HttpResponseInterface
    {
        $rows = $this->db->queryAssoc(
            "SELECT DISTINCT
                region AS `name`
            FROM
                weather.cities
            ORDER BY
                region"
        );

        return new JsonHttpResponse(['rows' => $rows]);
    }

    public function getCityWeather(HttpRequest $request): HttpResponseInterface
    {
        $cityId = $request->query["cityId"] ?? null;
        $startDate = $request->query["startDate"] ?? null;
        $endDate = $request->query["endDate"] ?? null;

        if (!is_string($cityId) || !ctype_digit($cityId)) {
            return new JsonHttpResponse(["error" => "'cityId' query paraméter kötelező"], 400);
        }

        if (!is_string($startDate) || !is_string($endDate)) {
            return new JsonHttpResponse(["error" => "'startDate' és 'endDate' query paraméter kötelező"], 400);
        }

        $startDateValue = $this->normalizeDate($startDate);
        $endDateValue = $this->normalizeDate($endDate);

        if ($startDateValue === null || $endDateValue === null) {
            return new JsonHttpResponse(["error" => "A dátum formátuma legyen YYYY-MM-DD"], 400);
        }

        if ($startDateValue > $endDateValue) {
            return new JsonHttpResponse(["error" => "'startDate' nem lehet nagyobb mint 'endDate'"], 400);
        }

        $rows = $this->db->queryAssoc(
            "SELECT
                CAST(minT.date AS CHAR) AS `date`,
                city.id AS `cityId`,
                city.name AS `cityName`,
                city.region AS `region`,
                minT.min_temp AS `minTemp`,
                maxT.max_temp AS `maxTemp`,
                prec.precipitation AS `precipitation`
            FROM
                weather.cities city
            JOIN
                weather.minimum_temperatures minT
                ON minT.city_id = city.id
            JOIN
                weather.maximum_temperatures maxT
                ON maxT.city_id = city.id
                AND maxT.date = minT.date
            JOIN
                weather.precipitation prec
                ON prec.city_id = city.id
                AND prec.date = minT.date
            WHERE
                city.id = :cityId
                AND minT.date BETWEEN :startDate AND :endDate
            ORDER BY
                minT.date",
            [
                ":cityId" => (int) $cityId,
                ":startDate" => $startDateValue,
                ":endDate" => $endDateValue,
            ],
        );

        return new JsonHttpResponse(["rows" => $rows]);
    }

    public function getRegionWeather(HttpRequest $request): HttpResponseInterface
    {
        $region = $request->query["region"] ?? null;
        $startDate = $request->query["startDate"] ?? null;
        $endDate = $request->query["endDate"] ?? null;

        if (!is_string($region) || trim($region) === "") {
            return new JsonHttpResponse(["error" => "'region' query paraméter kötelező"], 400);
        }

        if (!is_string($startDate) || !is_string($endDate)) {
            return new JsonHttpResponse(["error" => "'startDate' és 'endDate' query paraméter kötelező"], 400);
        }

        $startDateValue = $this->normalizeDate($startDate);
        $endDateValue = $this->normalizeDate($endDate);

        if ($startDateValue === null || $endDateValue === null) {
            return new JsonHttpResponse(["error" => "A dátum formátuma legyen YYYY-MM-DD"], 400);
        }

        if ($startDateValue > $endDateValue) {
            return new JsonHttpResponse(["error" => "'startDate' nem lehet nagyobb mint 'endDate'"], 400);
        }

        $rows = $this->db->queryAssoc(
            "SELECT
                CAST(minT.date AS CHAR) AS `date`,
                city.region AS `region`,
                ROUND(AVG(minT.min_temp), 2) AS `minTemp`,
                ROUND(AVG(maxT.max_temp), 2) AS `maxTemp`,
                ROUND(AVG(prec.precipitation), 2) AS `precipitation`
            FROM
                weather.cities city
            JOIN
                weather.minimum_temperatures minT
                ON minT.city_id = city.id
            JOIN
                weather.maximum_temperatures maxT
                ON maxT.city_id = city.id
                AND maxT.date = minT.date
            JOIN
                weather.precipitation prec
                ON prec.city_id = city.id
                AND prec.date = minT.date
            WHERE
                city.region = :region
                AND minT.date BETWEEN :startDate AND :endDate
            GROUP BY
                minT.date, city.region
            ORDER BY
                minT.date",
            [
                ":region" => $region,
                ":startDate" => $startDateValue,
                ":endDate" => $endDateValue,
            ],
        );

        return new JsonHttpResponse(["rows" => $rows]);
    }

    private function normalizeDate(string $date): ?int
    {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            return null;
        }

        $normalizedDate = str_replace("-", "", $date);

        return (int) $normalizedDate;
    }
}
