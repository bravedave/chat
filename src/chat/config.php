<?php
// file: src/chat/config.php
namespace chat;

use config as rootConfig;

class config extends rootConfig {  // noting: config extends global config classes
  const chat_db_version = 3;
  const label = 'Chat';  // general label for application

  const chat_max_tokens = 500;
  const chat_model = 'gpt-4o-mini';

  static function checkdatabase() {
    $dao = new dao\dbinfo;
    // $dao->debug = true;
    $dao->checkVersion('chat', self::chat_db_version);
  }

  static $OPENAI_API_KEY = '';

  public static function chat_initialize() {

    $path = static::defaultsPath();
    if (file_exists($path)) {

      $_a = [
        'openai_api_key' => '',
      ];

      $a = (object)array_merge($_a, (array)json_decode(file_get_contents($path)));
      static::$OPENAI_API_KEY = $a->openai_api_key;
    }
  }
}

config::chat_initialize();
