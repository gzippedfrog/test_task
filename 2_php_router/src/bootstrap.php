<?php

use App\Classes\Middlewares\RateLimiterMiddleware;
use App\Classes\Router;

$router = new Router();

$router
    ->get('/categories', function ($params) use ($router) {
        return $router->jsonResponse([
            'categories' => [
                'books',
                'electronics',
                'clothing'
            ]
        ]);
    })
    ->put('/newsletter/subscribe', function ($params) use ($router) {
        return $router->jsonResponse([
            'result' => 'subscribed',
            'params' => $params
        ]);
    }, [new RateLimiterMiddleware(10, 60)])
    ->get('/categories/{category_name}/products', function ($params) use ($router) {

        $category = $params['category_name'] ?? null;

        return $router->jsonResponse([
            'category' => $category,
            'products' => [
                "{$category}-item-1",
                "{$category}-item-2"
            ]
        ]);
    });