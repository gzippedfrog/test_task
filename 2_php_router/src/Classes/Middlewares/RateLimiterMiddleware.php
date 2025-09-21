<?php

namespace App\Classes\Middlewares;

use App\Interfaces\MiddlewareInterface;

class RateLimiterMiddleware implements MiddlewareInterface
{
    private $maxRequests;
    private $perSeconds;
    private $storagePath;

    public function __construct(int $maxRequests = 10, int $perSeconds = 60, ?string $storagePath = null)
    {
        $this->maxRequests = $maxRequests;
        $this->perSeconds = $perSeconds;
        $this->storagePath = $storagePath ?? sys_get_temp_dir() . '/php_rate_limiter.json';
    }

    /**
     * Получить IP адрес клиента
     * @return string
     */
    private function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Получить историю предыдущих запросов пользователей
     * @return array
     */
    private function loadData(): array
    {
        if (!file_exists($this->storagePath)) {
            return [];
        }

        $content = @file_get_contents($this->storagePath);

        if ($content === false) {
            return [];
        }

        return json_validate($content) ? json_decode($content, true) : [];
    }

    /**
     * Сохранить историю предыдущих запросов пользователей
     * @param array $data
     * @return void
     */
    private function saveData(array $data): void
    {
        @file_put_contents($this->storagePath, json_encode($data));
    }

    /**
     * Обработать запрос с учетом лимитов
     * @param array $params
     * @param callable $next
     */
    public function handle(array $params, callable $next)
    {
        $ip = $this->getIp();
        $now = time();
        $data = $this->loadData();

        if (!isset($data[$ip])) {
            $data[$ip] = [];
        }

        $window_start = $now - $this->perSeconds;
        $data[$ip] = array_filter($data[$ip], function ($ts) use ($window_start) {
            return $ts >= $window_start;
        });

        if (count($data[$ip]) >= $this->maxRequests) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Too Many Requests']);
            return null;
        }

        $data[$ip][] = $now;
        $this->saveData($data);

        return $next($params);
    }
}