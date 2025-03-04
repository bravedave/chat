<?php
// file: src/chat/dao/dto/chat.php
namespace chat\dao\dto;

use bravedave\dvc\dto;

class chat extends dto {

  public $id = 0;

  public string $created = '';
  public string $updated = '';

  public string $name = '';

  // rich data
  public array $lines = [];
}
