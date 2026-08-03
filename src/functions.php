<?php

// Die and Dump

function dd($value) {
    echo "<pre>";

    var_dump($value);

    echo "</pre>";

    die();
}

// Sanitize database output

function escape(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Return base app url

function base_url($path = null) {
    return BASE_URL . $path;
}

// Return base path

function base_path($path = null) {
    return BASE_PATH . $path;
}

// Return view path

function view(string $view) {
    return base_path("src/views/{$view}.php");
}

// Return asset path

function asset(string $path): string {
    return "/assets/" . ltrim($path, '/');
}

// Format date

function format_date(string $date, string $format = 'l j F Y H:i'): string {
    return escape(date($format, strtotime($date)));
}

// Get current year

function get_year() {
    return date('Y');
}