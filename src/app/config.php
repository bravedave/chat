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
  static $OPENAI_API_KEY = '';

  public static function initialize() {

    parent::initialize();

    $path = static::defaultsPath();
    if (file_exists($path)) {

      $_a = [
        'openai_api_key' => '',
      ];

      $a = (object)array_merge($_a, (array)json_decode(file_get_contents($path)));
      static::$OPENAI_API_KEY = $a->openai_api_key;
    }

    static::$PAGE_LAYOUT = 'left';
  }
}
