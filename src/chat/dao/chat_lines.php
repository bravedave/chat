<?php
// file : src/chat/dao/chat_lines.php
namespace bravedave\chat\dao;

use bravedave\dvc\dao;

class chat_lines extends dao {
  protected $_db_name = 'chat_lines';
  protected $template = dto\chat_lines::class;

  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
