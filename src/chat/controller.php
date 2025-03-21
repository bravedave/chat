<?php
// file: src/chat/controller.php
namespace bravedave\chat;

use bravedave\dvc\ServerRequest;
use Controller as rootController;

class controller extends rootController {
  protected function _index() {

    $this->data = (object)[
      'title' => $this->title = config::label,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => false,
      'aside' => config::index_set
    ];

    $this->renderBS5([
      'aside' => fn() => $this->load('chat-index'),
      'main' => fn() => $this->load('chat-matrix')
    ]);
  }

  protected function before() {

    config::checkdatabase();  // add this line
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
  }

  protected function postHandler() {

    $request = new ServerRequest;
    $action = $request('action');

    /*
    _brayworth_.fetch.post('chat', {
      action: 'chat-get-matrix'
    }).then( console.log);

    _brayworth_.fetch.post('chat', {
      action: 'chat-suggest-topic',
      id : 1
    }).then( console.log);
    */

    return match ($action) {
      'chat-delete' => handler::deleteChat($request),
      'chat-get-by-id' => handler::getChatByID($request),
      'chat-get-matrix' => handler::getMatrix($request),
      'chat-save' => handler::saveChat($request),
      'chat-send' => handler::sendChat($request),
      'chat-suggest-topic' => handler::suggestTopic($request),
      'markdown-to-html' => handler::markdownToHtml($request),
      default => parent::postHandler()
    };
  }

  public function about() {

    $this->title = config::label;

    $this->renderBS5([
      'aside' => fn() => $this->load('chat-index'),
      'main' => fn() => $this->load('Readme')
    ]);
  }

  public function edit($id = 0) {

    // tip : the structure is available in the view at $dto
    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\chat
    ];

    if ($id = (int)$id) {

      $dao = new dao\chat;
      $this->data->dto = $dao($id);
      $this->data->title .= ' edit';
    }

    $this->load('chat-edit');
  }

  public function view($id = 0) {

    if ($id = (int)$id) {

      $dao = new dao\chat;
      if ($dto = $dao($id)) {

        $this->data = (object)[
          'title' => $this->title = config::label,
          'dto' => $dto
        ];

        $this->load('chat-view');
      } else {

        print 'not found';
      }
    } else {

      $this->data = (object)[
        'title' => $this->title = config::label,
        'dto' => new dao\dto\chat
      ];

      $this->load('chat-view');
      // print 'invalid id';
    }
  }
}
