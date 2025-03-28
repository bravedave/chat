<?php
// file: src/chat/dao/dto/chat.php
namespace bravedave\chat\dao\dto;

use bravedave\dvc\dto;

class chat extends dto {

  public $id = 0;

  public string $created = '';
  public string $updated = '';

  public string $name = '';
  public int $assistant = 0;
  public int $users_id = 0;


  // rich data
  public array $lines = [];
}
