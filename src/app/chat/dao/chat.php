<?php
// file : src/chat/dao/chat.php
namespace chat\dao;

use bravedave\dvc\{dao, dtoSet};

class chat extends dao {
  protected $_db_name = 'chat';
  protected $template = dto\chat::class;

  public function delete($id) {

    // delete all chat lines, don't blow the cache
    $sql = sprintf(
      'SELECT id FROM `chat_lines` WHERE `chat_id` = %d',
      $id
    );

    (new dtoSet)($sql, fn($dto) => (new chat_lines)->delete($dto->id));
    return parent::delete($id);
  }

  public function getMatrix(): array {

    $sql = 'SELECT
      c.`id`,
      c.`name`,
      c.`updated`,
      (SELECT count(*) FROM `chat_lines` WHERE `chat_id` = c.`id`) as linecount
      FROM `chat` c
      ORDER BY c.`id`';

    return (new dtoSet)($sql);
  }

  public function getRichData(dto\chat $dto): dto\chat {

    $sql = sprintf(
      'SELECT * FROM `chat_lines` WHERE `chat_id` = %d ORDER BY `id`',
      $dto->id
    );

    $dto->lines = (new dtoSet)($sql);
    return $dto;
  }

  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
