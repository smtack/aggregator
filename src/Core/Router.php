<?php

namespace Core;

class Router
{
    public function __construct(public array $routes = [])
    {
        $this->routes = $routes;
    }

    public function lookup(?string $uri): array|false
    {
        foreach ($this->routes as $route => $action) {
            $pattern = preg_replace(
                '/\{[^}]+\}/',
                '([^/]+)',
                $route
            );

            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                return [
                    'action' => $action,
                    'params' => $matches,
                ];
            }
        }

        return false;
    }

    public function route(string $uri)
    {
        $route = $this->lookup($uri);

        if ($route === false) {
            $this->abort(404);
        }

        [$controller, $method] = $route['action'];

        $instance = new $controller();

        $instance->$method(...$route['params']);
    }

    public function abort(int $code)
    {
        http_response_code($code);

        require view("errors/{$code}");

        exit;
    }
}