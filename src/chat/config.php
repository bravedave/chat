<?php
// file: src/chat/config.php
namespace bravedave\chat;

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
}
