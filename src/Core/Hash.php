<?php

namespace Core;

class Hash
{
    public function random(int $num)
    {
        return bin2hex(random_bytes($num));
    }

    public function generate(string $token)
    {
        return $_SESSION[$token] = $this->random(64);
    }

    public function check(string $token, string $name)
    {
        if(isset($_SESSION[$name]) && hash_equals($_SESSION[$name], $token)) {
            unset($_SESSION[$name]);

            return true;
        }

        return false;
    }
}