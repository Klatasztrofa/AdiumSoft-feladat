<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework;

use Closure;
use Exception;

/**
 * Egyszerű HTTP router.
 */
class Router
{
    /**
     * @var array<string, array<string, Closure(HttpRequest):HttpResponseInterface>>
     */
    private array $routeGroups = [];

    /**
     * Regisztrál egy új végpontot.
     *
     * Az egyszerűség kedvéért most path paramétereket nem kezelünk, csak
     * pontos egyezőséget figyelünk.
     *
     * @param string $method HTTP metódus, pl. "GET", "POST"
     * @param string $path A végpont elérési útja, az URL path része, pl. "/hello"
     * @param Closure(HttpRequest):HttpResponseInterface $handler
     */
    public function addRoute(string $method, string $path, Closure $handler): void
    {
        $routeGroup = $this->routeGroups[$path] ?? [];
        $routeGroup[$method] = $handler;
        $this->routeGroups[$path] = $routeGroup;
    }

    /**
     * Elvégzi a HTTP kérés feldolgozását és a válasz visszaküldését.
     */
    public function handleRequest(): void
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $route = $_SERVER["REQUEST_URI"];

        // A REQUEST_URI-ban a query string is benne van, köszi PHP.
        [$path] = explode("?", $route);
        $path = rawurldecode($path);

        $routeGroup = $this->routeGroups[$path] ?? null;
        if ($routeGroup === null) {
            $res = new JsonHttpResponse(["error" => "nem található"], 404);
            $this->sendResponse($res);
            return;
        }
        $handler = $routeGroup[$method] ?? null;
        if ($handler === null) {
            $res = new JsonHttpResponse(["error" => "{$method} nem engedélyezett"], 405);
            $this->sendResponse($res);
            return;
        }
        $request = HttpRequest::fromGlobals();
        try {
            $res = $handler($request);
        } catch (Exception $e) {
            $stack = $e->getTraceAsString();
            $res = new JsonHttpResponse([
                "error" => "szerveroldali hiba: {$e->getMessage()}",
                // Nyilván ilyet valójában soha nem teszünk bele, de a
                // fejlesztés közben segíthet.
                "stackTrace" => $stack,
            ], 500);
        }
        $this->sendResponse($res);
    }

    private function sendResponse(HttpResponseInterface $response): void
    {
        http_response_code($response->getStatus());

        // Most a példában csak ez az egy van.
        if ($response instanceof JsonHttpResponse) {
            header("Content-Type: application/json");
        }

        $body = $response->getBody();
        if ($body !== null) {
            echo $body;
        }
    }
}
