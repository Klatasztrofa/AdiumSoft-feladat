<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework;

/**
 * HTTP válasz, JSON törzzsel.
 */
class JsonHttpResponse implements HttpResponseInterface
{
    /**
     * @param mixed $body A válasz törzse. Visszaküldés előtt JSON szövegként lesz megformázva.
     * @param int $status HTTP státusz kód (100 - 599).
     */
    public function __construct(
        private readonly mixed $body,
        private readonly int $status = 200,
    ) {
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getBody(): string|null
    {
        $json = json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $errno = json_last_error();
            $errmsg = json_last_error_msg();
            return "json_encode() hiba: {$errmsg} ({$errno})";
        }
        return $json;
    }
}
