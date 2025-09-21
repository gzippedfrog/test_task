<?php

namespace App\Classes;

class Router
{
    private $routes = [];

    /**
     * Добавить маршрут
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @param array $middleware
     * @return self
     */
    public function add(string $method, string $path, callable $handler, array $middleware = []): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];

        return $this;
    }

    /**
     * Добавить GET маршрут
     * @param string $path
     * @param callable $handler
     * @param array $middleware
     * @return self
     */
    public function get(string $path, callable $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    /**
     * Добавить POST маршрут
     * @param string $path
     * @param callable $handler
     * @param array $middleware
     * @return self
     */
    public function post(string $path, callable $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    /**
     * Добавить PUT маршрут
     * @param string $path
     * @param callable $handler
     * @param array $middleware
     * @return self
     */
    public function put(string $path, callable $handler, array $middleware = []): self
    {
        return $this->add('PUT', $path, $handler, $middleware);
    }

    /**
     * Добавить DELETE маршрут
     * @param string $path
     * @param callable $handler
     * @param array $middleware
     * @return self
     */
    public function delete(string $path, callable $handler, array $middleware = []): self
    {
        return $this->add('DELETE', $path, $handler, $middleware);
    }

    /**
     * Обработать запрос
     * @param string $method
     * @param string $path
     * @param array $params
     */
    public function dispatch(string $method, string $path, array $params = [])
    {
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertPathToRegex($route['path']);

            if (preg_match($pattern, $path, $matches)) {
                $route_params = $this->extractParams($matches);
                $all_params = array_merge($params, $route_params);

                $handler = $route['handler'];

                $middleware_stack = $route['middleware'];
                $next = function ($p) use ($handler) {
                    return $handler($p);
                };

                for ($i = count($middleware_stack) - 1; $i >= 0; $i--) {
                    $mw = $middleware_stack[$i];
                    $next = function ($p) use ($mw, $next) {
                        return $mw->handle($p, $next);
                    };
                }

                $result = $next($all_params);

                // Если обработчик вернул true, это означает что ответ уже отправлен
                if ($result === true) {
                    return;
                }

                return $result;
            }
        }

        $this->jsonResponse(['error' => 'Not Found'], 404);
    }

    /**
     * Преобразовать путь с динамической частью в регулярное выражение.
     * Например "/categories/{category_name}/products" -> "#^/categories/(?P<category_name>[^/]+)/products$#"
     * @param string $path
     * @return string
     */
    private function convertPathToRegex(string $path): string
    {
        $regex = preg_replace('#\{([\w]+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    /**
     * Извлечь параметры маршрута
     * @param array $matches
     * @return array
     */
    private function extractParams(array $matches): array
    {
        $params = [];

        foreach ($matches as $key => $val) {
            if (!is_int($key)) {
                $params[$key] = $val;
            }
        }

        return $params;
    }

    /**
     * Возвращает JSON ответ
     * @param array $data
     * @param int $code
     * @return void
     */
    public function jsonResponse(array $data, int $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, true);
    }
}
