<?php

// Sanitize database output

function escape($string) {
  return htmlentities($string, ENT_QUOTES, 'ISO-8859-15');
}

// Flash function

function flash($name = '', $message = '', $class = '') {
  if(!empty($name)) {
    if(!empty($message) && empty($_SESSION['name'])) {
      if(!empty($_SESSION['name'])) {
        unset($_SESSION['name']);
      }

      if(!empty($_SESSION[$name . '_class'])) {
        unset($_SESSION[$name . '_class']);
      }

      $_SESSION[$name] = $message;
      $_SESSION[$name . '_class'] = $class;
    } else if(empty($message) && !empty($_SESSION[$name])) {
      $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';

      $output = '<div>';
      $output .= '<div class="' . $class . '">';
      $output .= '<p>' . $_SESSION[$name] . '</p>';

      if($class == 'flash') {
        $output .= '<span class="close">&times;</span>';
      }

      $output .= '</div>';

      echo $output;

      unset($_SESSION[$name]);
      unset($_SESSION[$name . '_class']);
    }
  }
}

// Return BASE_URL + optional location

function base_url($location = null) {
  if(!$location) {
    return BASE_URL;
  } else {
    return BASE_URL . $location;
  }
}

// Create random bytes

function random($num) {
  return bin2hex(random_bytes($num));
}

// Error Handler

function errorHandler() {
  if(error_reporting()) {
    include_once __DIR__ . '/../public/views/errors/error.php';

    exit();
  }
}

// Create token

function generate($token) {
  return $_SESSION[$token] = random(64);
}

// Check token

function check($token, $name) {
  if(isset($_SESSION[$name]) && hash_equals($_SESSION[$name], $token)) {
    unset($_SESSION[$name]);

    return true;
  }

  return false;
}