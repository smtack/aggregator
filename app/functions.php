<?php
// Sanitize Input and Output

function escape($io) {
  return htmlentities($io, ENT_QUOTES, 'ISO-8859-15');
}

// Error function

function error($name = '', $message = '', $class = 'error') {
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

      echo('
        <div class="' . $class . '">
          <p>' . $_SESSION[$name] . '</p>
        </div>
      ');

      unset($_SESSION[$name]);
      unset($_SESSION[$name . '_class']);
    }
  }
}

// Flash function

function flash($name = '', $message = '', $class = 'flash') {
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

      echo('
        <div class="' . $class . '">
          <p>' . $_SESSION[$name] . '</p>
          <span class="close">&times;</span>
        </div>
      ');

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

// Check for a value in a multidimensional array

function findValue($array, $key, $value) {
  foreach($array as $item) {
    if(is_array($item) && findValue($item, $key, $value)) {
      return true;
    }

    if(isset($item[$key]) && $item[$key] == $value) {
      return true;
    }
  }

  return false;
}

// Error Handler

function errorHandler() {
  if(error_reporting()) {
    include_once 'public/views/errors/error.php';

    die();
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