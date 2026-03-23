<?php

declare(strict_types=1);

namespace ADS\TakeHome\Framework;

use PDO;
use PDOException;

/**
 * MySQL adatbáziskapcsolat.
 */
class MySQL
{
    private readonly PDO $conn;

    public function __construct(
        string $host,
        string $schema,
        string $username,
        string $password,
        int $port = 3306,
    ) {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$schema}", $username, $password);
        // Lekérdezés hiba esetén PHP kivétel legyen.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn = $pdo;
    }

    /**
     * Végrehajtja a lekérdezést és az eredményt visszaadja asszociatív tömbök
     * listájaként.
     *
     * A paraméter behelyettesítés módszere hasonlít a PDO-ban látotthoz
     * (https://www.php.net/manual/en/pdo.prepare.php), de pozícionális
     * paramétereket nem kezel.
     *
     * @param array<string, scalar|null> $params Behelyettesítendő paraméterek,
     * név szerint. Pozícionális paramétereket nem fogad el!
     *
     * @return list<array<string, scalar|null>>
     *
     * @throws PDOException ha a lekérdezés sikertelen.
     */
    public function queryAssoc(string $sql, array $params = []): array
    {
        $sql = $this->substituteParams($sql, $params);
        $stmt = $this->conn->query($sql, PDO::FETCH_ASSOC);
        assert($stmt !== false);
        return $stmt->fetchAll();
    }

    /**
     * @param array<string, scalar|null> $params
     */
    private function substituteParams(string $sql, array $params): string
    {
        foreach ($params as $paramName => $paramValue) {
            $paramValue = match (true) {
                is_string($paramValue) => $this->conn->quote(strval($paramValue)),
                is_null($paramValue) => "NULL",
                is_bool($paramValue) => $paramValue ? "TRUE" : "FALSE",
                default => strval($paramValue), // számok
            };

            // Ilyesmit valójában soha nem csinálunk, itt csak az egyszerűség
            // kedvéért van így. Ha string paraméter van, mindig paraméterezett
            // SQL lekérdezéseket használunk.
            $sql = str_replace($paramName, $paramValue, $sql);
        }
        return $sql;
    }
}
