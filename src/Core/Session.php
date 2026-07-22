<?php

namespace Core;

class Session
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function put(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key)
    {
        return (bool) $this->get($key);
    }

    public function destroy()
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $params = session_get_cookie_params();

        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    public function regenerate(bool $delete_old_session = true): void
    {
        session_regenerate_id($delete_old_session);
    }

    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public function hasFlash(string $key)
    {
        return $_SESSION['_flash'][$key] ?? false;
    }

    public function unflash()
    {
        unset($_SESSION['_flash']);
    }
}