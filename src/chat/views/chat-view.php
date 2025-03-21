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

/** @var dao\dto\chat $dto */

?>

<form id="<?= $_form = strings::rand(); ?>">
  <input type="hidden" name="action" value="chat-send">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="js-chatbox overflow-y-auto px-2 mb-2" style="height:calc(100vh - 12.5rem)">
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
            <div class="alert alert-light" role="alert"><?= strings::markdownToHtml($line->content) ?></div>
          </div>
        </div>

    <?php }
    } ?>
  </div>

  <div class="row">

    <div class="col">
      <textarea class="form-control" name="user_input"
        placeholder="Type a message..."
        autocomplete="off"
        rows="2"
        autofocus></textarea>
    </div>

    <div class="col-auto">
      <button class="btn btn-outline-primary" type="submit">send</button>
    </div>
  </div>
  <style>
    #<?= $_form . ' ' ?>.js-chatbox pre {
      font-size: 12px;
      border: 1px solid silver;
      padding: .5rem;
      background-color: black;
      color: white;
    }
  </style>
</form>
<script>
  (_ => {
    const form = $('#<?= $_form ?>');
    const chatbox = form.find('.js-chatbox');

    form.on('submit', function(e) {

      try {

        const txt = form.find('textarea[name="user_input"]').val();
        chatbox.append(`<div class="row gx-2"><div class="col-auto">
            <div class="alert alert-primary" role="alert">${txt.toHtml()}</div>
          </div></div>`);
        chatbox.scrollTop(chatbox[0].scrollHeight);

        _.fetch.post.form(_.url('<?= $this->route ?>'), this)
          .then(d => {

            if ('ack' == d.response) {

              const div = $(`<div class="row gx-2">
                  <div class="col"></div>
                  <div class="col-auto">
                    <div class="alert alert-light" role="alert">${d.data.response}</div>
                  </div></div>`)
              chatbox.append(div);
              chatbox.scrollTop(chatbox[0].scrollHeight);
              form.trigger('chat-updated', d.chat);

              form.find('textarea[name="user_input"]').val('').focus();

              const payload = {
                action: 'markdown-to-html',
                markdown: d.data.response,
              };
              // console.log(payload);

              _.fetch.post(_.url('<?= $this->route ?>'), payload).then(d => {

                if ('ack' == d.response) {

                  // console.log(d);
                  div.find('.alert').html(d.output);
                  chatbox.scrollTop(chatbox[0].scrollHeight);
                } else {

                  _.growl(d);
                }
              });

            } else {

              console.log(d);
              _.growl(d);
            }
          });
      } catch (error) {

        console.error(error);
      }

      return false;
    });

    _.ready(() => {
      chatbox.scrollTop(chatbox[0].scrollHeight);
      form.find('[name="user_input"]').focus();
    });
  })(_brayworth_);
</script>