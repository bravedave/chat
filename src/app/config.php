<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\dvc\logger;

class config extends bravedave\dvc\config {

  static $PAGE_LAYOUT = 'left';
  static $OPENAI_API_KEY = '';

  public static function chat_initialize() {

    $path = static::defaultsPath();
    if (file_exists($path)) {

      $_a = [
        'OPENAI_API_KEY' => '',
      ];

      $a = (object)array_merge($_a, (array)json_decode(file_get_contents($path)));
      static::$OPENAI_API_KEY = $a->OPENAI_API_KEY;
    }
  }
}

config::chat_initialize();
