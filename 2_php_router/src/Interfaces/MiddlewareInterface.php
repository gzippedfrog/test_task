<?php

namespace App\Interfaces;

interface MiddlewareInterface {
    /**
     * Обработать запрос
     * @param array $params
     * @param callable $next
     * @return void
     */
    public function handle(array $params, callable $next);
}