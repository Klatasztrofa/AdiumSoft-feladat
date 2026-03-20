<?php

namespace ADS\TakeHome;

include __DIR__ . "/../vendor/autoload.php";

use ADS\TakeHome\Controllers\ExampleController;
use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\MySQL;
use ADS\TakeHome\Framework\Router;

$router = new Router();

$mysql = new MySQL("db", "weather", "root", "root");
$helloController = new ExampleController(greeting: "Hello", db: $mysql);
$weatherController = new WeatherController(db: $mysql);

$router->addRoute("GET", "/hello", $helloController->sayHello(...));
$router->addRoute("GET", "/budapest", $helloController->getBudapestMaxTempCount(...));

$router->addRoute("GET", "/cities", $weatherController->getCities(...));
$router->addRoute("GET", "/regions", $weatherController->getRegions(...));
$router->addRoute("GET", "/weather/by-city", $weatherController->getCityWeather(...));
$router->addRoute("GET", "/weather/by-region", $weatherController->getRegionWeather(...));

$router->handleRequest();
