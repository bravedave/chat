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

use bravedave\dvc\strings;

/** @var dao\dto\chat $dto */

?>

<form id="<?= $_form = strings::rand(); ?>">
  <input type="hidden" name="action" value="chat-send">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="js-chatbox overflow-y-auto px-2 mb-2" style="height:calc(100vh - 11.5rem)">
    <?php foreach ($dto->lines as $line) {

      if ('user' == $line->role) { ?>

        <div class="row gx-2">
          <div class="col-auto">
            <div class="alert alert-primary" role="alert"><?= strings::text2html($line->content) ?></div>
          </div>
        </div>
      <?php } elseif ('assistant' == $line->role) { ?>

        <div class="row gx-2">
          <div class="col"></div>
          <div class="col-auto">
            <div class="alert alert-light" role="alert"><?= strings::text2html($line->content) ?></div>
          </div>
        </div>

    <?php }
    } ?>
  </div>

  <div class="row">

    <div class="col">
      <input type="text" class="form-control" name="user_input"
        placeholder="Type a message..."
        autocomplete="off"
        autofocus>
    </div>

    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">send</button>
    </div>
  </div>
</form>
<script>
  (_ => {
    const form = $('#<?= $_form ?>');
    const chatbox = form.find('.js-chatbox');

    form
      .on('submit', function(e) {

        const txt = form.find('input[name="user_input"]').val();
        chatbox.append(`<div class="row gx-2"><div class="col-auto">
          <div class="alert alert-primary" role="alert">${txt.toHtml()}</div>
        </div></div>`);
        chatbox.scrollTop(chatbox[0].scrollHeight);

        _.fetch.post.form(_.url('<?= $this->route ?>'), this)
          .then(d => {

            if ('ack' == d.response) {

              chatbox.append(`<div class="row gx-2">
                <div class="col"></div>
                <div class="col-auto">
                  <div class="alert alert-light" role="alert">${d.data.response}</div>
                </div></div>`);
              chatbox.scrollTop(chatbox[0].scrollHeight);
              form.trigger('chat-updated', d.chat);

              form.find('input[name="user_input"]').val('').focus();
            } else {

              console.log(d);
              _.growl(d);
            }
          });

        return false;
      });

    _.ready(() => {
      chatbox.scrollTop(chatbox[0].scrollHeight);
      form.find('[name="user_input"]').focus();
    });
  })(_brayworth_);
</script>