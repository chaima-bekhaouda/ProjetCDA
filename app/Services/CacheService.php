<?php

namespace App\Services;

class CacheService
{
    // Redis cache simple, si Redis n'est pas joignable on continue sans erreur.
    private ?string $host;
    private int $port;
    private $socket = null;
    private bool $available = false;

    public function __construct(?string $host = null, ?int $port = null)
    {
        $this->host = $host ?? ($_ENV['REDIS_HOST'] ?? '127.0.0.1');
        $this->port = $port ?? (int) ($_ENV['REDIS_PORT'] ?? '6379');
        $this->connect();
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function get(string $key): mixed
    {
        if (!$this->available) {
            return null;
        }

        $response = $this->executeCommand(['GET', $key]);
        if ($response === false || $response === null) {
            return null;
        }

        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $response;
    }

    public function set(string $key, mixed $value, int $ttl = 300): bool
    {
        if (!$this->available) {
            return false;
        }

        $payload = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $response = $this->executeCommand(['SET', $key, $payload, 'EX', (string) $ttl]);

        return $response === 'OK';
    }

    public function delete(string $key): bool
    {
        if (!$this->available) {
            return false;
        }

        $response = $this->executeCommand(['DEL', $key]);

        return (int) $response > 0;
    }

    public function deleteByPrefix(string $prefix): int
    {
        if (!$this->available) {
            return 0;
        }

        $pattern = $prefix . '*';
        $keys = $this->keys($pattern);
        $deleted = 0;

        foreach ($keys as $key) {
            if ($this->delete($key)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    public function keys(string $pattern): array
    {
        if (!$this->available) {
            return [];
        }

        $response = $this->executeCommand(['KEYS', $pattern]);
        if (!is_array($response)) {
            return [];
        }

        return $response;
    }

    public function remember(string $key, callable $callback, int $ttl = 300): mixed
    {
        // cache-aside : on tente d'abord de lire le cache,
        // puis on génère et stocke la donnée si elle n'existe pas.
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    private function connect(): void
    {
        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 0.2);
        if (is_resource($this->socket)) {
            $this->available = true;
        }
    }

    private function executeCommand(array $args): mixed
    {
        if (!$this->available || !is_resource($this->socket)) {
            return false;
        }

        $payload = '*' . count($args) . "\r\n";
        foreach ($args as $arg) {
            $payload .= '$' . strlen((string) $arg) . "\r\n" . $arg . "\r\n";
        }

        fwrite($this->socket, $payload);

        return $this->readResponse();
    }

    private function readResponse(): mixed
    {
        $line = fgets($this->socket);
        if ($line === false) {
            $this->available = false;
            return false;
        }

        $prefix = $line[0];
        $data = trim(substr($line, 1));

        return match ($prefix) {
            '+' => $data,
            ':' => (int) $data,
            '$' => $this->readBulk($data),
            '-' => false,
            '*' => $this->readMultiBulk($data),
            default => false,
        };
    }

    private function readMultiBulk(string $count): mixed
    {
        $count = (int) $count;
        if ($count < 0) {
            return null;
        }

        $values = [];
        for ($i = 0; $i < $count; $i++) {
            $values[] = $this->readResponse();
        }

        return $values;
    }

    private function readBulk(string $length): mixed
    {
        $length = (int) $length;
        if ($length < 0) {
            return null;
        }

        $payload = fread($this->socket, $length + 2);
        if ($payload === false) {
            $this->available = false;
            return false;
        }

        return trim(substr($payload, 0, $length));
    }
}
