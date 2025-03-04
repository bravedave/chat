<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace chat;

use bravedave\dvc\{json, logger, ServerRequest};

final class handler {

  static function deleteChat(ServerRequest $request): json {

    $action = $request('action');
    $id = (int)$request('id');
    if ($id) {

      (new dao\chat)->delete($id);;
      return json::ack($action);
    }

    return json::nak($action);
  }

  static function getChatByID(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      if ($dto = (new dao\chat)($id)) {

        return json::ack($action, $dto);
      }
    }

    return json::nak($action);
  }

  static function getMatrix(ServerRequest $request): json {

    $action = $request('action');

    $dao = new dao\chat;
    return json::ack($action, $dao->getMatrix());
  }

  static function saveChat(ServerRequest $request): json {

    $action = $request('action');

    $a = [
      'name' => $request('name')
    ];

    $id = (int)$request('id');
    $dao = new dao\chat;
    if ($id) {

      if ($dto = $dao->getByID($id)) {

        $dao->UpdateByID($a, $id);
        return json::ack($action, $dao($id));
      }
    } else {

      $id = $dao->Insert($a);
      return json::ack($action, $dao($id));
    }

    return json::nak($action);
  }

  static function sendChat(ServerRequest $request): json {

    $action = $request('action');
    $message = $request('user_input');
    $id = (int)$request('id');

    if ($id) {

      if ($dto = (new dao\chat)($id)) {

        /** @var dao\dto\chat $dto */

        if ($message) {

          $chatLines = new dao\chat_lines;
          $a = [
            'chat_id' => $id,
            'role' => 'user',
            'content' => $message
          ];
          $chatLines->Insert($a);

          $aMessage = [
            ["role" => "system", "content" => "You are a helpful assistant."],
          ];
          foreach ($dto->lines as $line) {

            $aMessage[] = [
              "role" => $line->role,
              "content" => $line->content
            ];
          }
          $aMessage[] = ["role" => "user", "content" => $message];
          // logger::dump($aMessage);
          // return json::nak($action);

          $chat = new chat($aMessage);
          $response = $chat();
          if ($response['error'] ?? false) return json::nak($action, $response['error']);

          if ($response['response'] ?? false) {

            $a = [
              'chat_id' => $id,
              'role' => 'assistant',
              'content' => $response['response']
            ];
            $chatLines->Insert($a);
          }

          return json::ack($action, $response)
            ->add('chat', (new dao\chat)($id));;
        }
      }
    } else {

      if ($message) {

        // $chatLines = new dao\chat_lines;
        // $a = [
        //   'chat_id' => $id,
        //   'role' => 'user',
        //   'content' => $message
        // ];
        // $chatLines->Insert($a);

        $aMessage = [
          ["role" => "system", "content" => "You are a helpful assistant."],
          ["role" => "user", "content" => $message]
        ];
        // logger::dump($aMessage);
        // return json::nak($action);

        $chat = new chat($aMessage);
        $response = $chat();
        if ($response['error'] ?? false) return json::nak($action, $response['error']);

        if ($response['response'] ?? false) {

          /*--- ---[get a topic for the chat]--- ---*/
          $aMessage[] = [
            "role" => "user",
            "content" => 'Suggest a short and precise conversation topic (3-5 words) based on this exchange.'
          ];

          $chat = new chat($aMessage);
          $_response = $chat();
          if ($_response['error'] ?? false) return json::nak($action, $_response['error']);

          $newChat = [
            'name' => trim($_response['response'], '"'),
          ];
          $id = (new dao\chat)->Insert($newChat);
          /*--- ---[/get a topic for the chat]--- ---*/

          $chatLines = new dao\chat_lines;
          $a = [
            'chat_id' => $id,
            'role' => 'user',
            'content' => $message
          ];
          $chatLines->Insert($a);
          $a = [
            'chat_id' => $id,
            'role' => 'assistant',
            'content' => $response['response']
          ];
          $chatLines->Insert($a);

          return json::ack($action, $response)
            ->add('chat', (new dao\chat)($id));
        }
      }
    }

    return json::nak($action);
  }

  static function suggestTopic(ServerRequest $request): json {

    $action = $request('action');
    $id = (int)$request('id');
    if ($id) {

      if ($dto = (new dao\chat)($id)) {

        /** @var dao\dto\chat $dto */
        $aMessage = [
          ["role" => "system", "content" => "You are a helpful assistant."],
        ];
        foreach ($dto->lines as $line) {

          $aMessage[] = [
            "role" => $line->role,
            "content" => $line->content
          ];
        }
        $aMessage[] = [
          "role" => "user",
          "content" => 'Suggest a short and precise conversation topic (3-5 words) based on this exchange.'
        ];
        // logger::dump($aMessage);
        // return json::nak($action);

        $chat = new chat($aMessage);
        $response = $chat();
        if ($response['error'] ?? false) return json::nak($action, $response['error']);
        return json::ack($action, $response);
      }
    }

    return json::nak($action);
  }
}
