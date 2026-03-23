<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\MySQL;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WeatherControllerTest extends TestCase
{
    private WeatherController $controller;

    protected function setUp(): void
    {
        $this->controller = new WeatherController(
            new MySQL("db", "weather", "root", "root"),
        );
    }

    #[Test]
    public function getCitiesReturnsRows(): void
    {
        $response = $this->controller->getCities();

        $this->assertSame(200, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertArrayHasKey("rows", $body);
        $this->assertNotEmpty($body["rows"]);
        $this->assertArrayHasKey("id", $body["rows"][0]);
        $this->assertArrayHasKey("name", $body["rows"][0]);
        $this->assertArrayHasKey("region", $body["rows"][0]);
    }

    #[Test]
    public function getRegionsReturnsRows(): void
    {
        $response = $this->controller->getRegions();

        $this->assertSame(200, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertArrayHasKey("rows", $body);
        $this->assertNotEmpty($body["rows"]);
        $this->assertArrayHasKey("name", $body["rows"][0]);
    }

    #[Test]
    public function getCityWeatherRequiresCityId(): void
    {
        $request = new HttpRequest([
            "startDate" => "2023-01-01",
            "endDate" => "2023-01-07",
        ]);

        $response = $this->controller->getCityWeather($request);

        $this->assertSame(400, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertSame("'cityId' query paraméter kötelező", $body["error"]);
    }

    #[Test]
    public function getCityWeatherReturnsRows(): void
    {
        $request = new HttpRequest([
            "cityId" => "1",
            "startDate" => "2023-01-01",
            "endDate" => "2023-01-07",
        ]);

        $response = $this->controller->getCityWeather($request);

        $this->assertSame(200, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertArrayHasKey("rows", $body);
        $this->assertNotEmpty($body["rows"]);
        $this->assertArrayHasKey("date", $body["rows"][0]);
        $this->assertArrayHasKey("minTemp", $body["rows"][0]);
        $this->assertArrayHasKey("maxTemp", $body["rows"][0]);
        $this->assertArrayHasKey("precipitation", $body["rows"][0]);
    }

    #[Test]
    public function getRegionWeatherRequiresRegion(): void
    {
        $request = new HttpRequest([
            "startDate" => "2023-01-01",
            "endDate" => "2023-01-07",
        ]);

        $response = $this->controller->getRegionWeather($request);

        $this->assertSame(400, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertSame("'region' query paraméter kötelező", $body["error"]);
    }

    #[Test]
    public function getRegionWeatherReturnsRows(): void
    {
        $request = new HttpRequest([
            "region" => "Dunántúl",
            "startDate" => "2023-01-01",
            "endDate" => "2023-01-07",
        ]);

        $response = $this->controller->getRegionWeather($request);

        $this->assertSame(200, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertArrayHasKey("rows", $body);
        $this->assertNotEmpty($body["rows"]);
        $this->assertArrayHasKey("date", $body["rows"][0]);
        $this->assertArrayHasKey("region", $body["rows"][0]);
        $this->assertArrayHasKey("minTemp", $body["rows"][0]);
        $this->assertArrayHasKey("maxTemp", $body["rows"][0]);
        $this->assertArrayHasKey("precipitation", $body["rows"][0]);
    }

    #[Test]
    public function getCityWeatherReturnsErrorWhenStartDateIsAfterEndDate(): void
    {
        $request = new HttpRequest([
            "cityId" => "1",
            "startDate" => "2023-01-07",
            "endDate" => "2023-01-01",
        ]);

        $response = $this->controller->getCityWeather($request);

        $this->assertSame(400, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertSame("'startDate' nem lehet nagyobb mint 'endDate'", $body["error"]);
    }

    #[Test]
    public function getCityWeatherReturnsExpectedFirstRowValues(): void
    {
        $request = new HttpRequest([
            "cityId" => "1",
            "startDate" => "2023-01-01",
            "endDate" => "2023-01-07",
        ]);

        $response = $this->controller->getCityWeather($request);

        $this->assertSame(200, $response->getStatus());

        $body = $this->decodeResponse($response);

        $this->assertArrayHasKey("rows", $body);
        $this->assertNotEmpty($body["rows"]);

        $firstRow = $body["rows"][0];

        $this->assertSame("20230101", $firstRow["date"]);
        $this->assertSame(1, $firstRow["cityId"]);
        $this->assertSame("Budapest", $firstRow["cityName"]);
        $this->assertSame("Közép-Magyarország", $firstRow["region"]);
        $this->assertSame(3.4, $firstRow["minTemp"]);
        $this->assertSame(8.5, $firstRow["maxTemp"]);
        $this->assertSame(0, $firstRow["precipitation"]);
    }

    /**
    * @return array<string, mixed>
    */
    private function decodeResponse(HttpResponseInterface $response): array
    {
        $body = $response->getBody();

        $this->assertIsString($body);

        $decodedBody = json_decode($body, true);

        $this->assertIsArray($decodedBody);

        return $decodedBody;
    }
}
