<?php
// file: src/chat/dao/dto/chat_lines.php
namespace chat\dao\dto;

use bravedave\dvc\dto;

class chat_lines extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';

  public $chat_id = 0;
  public $role = '';
  public $content = '';
}
