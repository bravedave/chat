<?php
// file : src/chat/dao/chat.php
namespace bravedave\chat\dao;

use bravedave\dvc\{dao, dtoSet};
use currentUser;

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

    $sql = sprintf(
      'SELECT
      c.`id`,
      c.`name`,
      c.`assistant`,
      c.`updated`,
      (SELECT count(*) FROM `chat_lines` WHERE `chat_id` = c.`id`) as linecount
      FROM `chat` c
      WHERE users_id = %d
      ORDER BY c.`id`',
      currentUser::id()
    );

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
    $a['users_id'] = currentUser::id();
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
