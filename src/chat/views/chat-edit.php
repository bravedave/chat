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

use theme;

/** @var dao\dto\chat $dto */
?>
<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <input type="hidden" name="action" value="chat-save">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
        </div>
        <div class="modal-body">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="<?= $_uid = strings::rand() ?>"
              placeholder="chat name" name="name"
              value="<?= htmlentities($dto->name) ?>" required autofocus>
            <label for="<?= $_uid ?>">chat name</label>
          </div>

          <div class="form-floating mb-3">
            <select class="form-select" name="assistant"
              id="<?= $_uid = strings::rand() ?>" placeholder="assistant type">
              <option value="0" <?= chat::helpful_assistant == $dto->assistant ? 'selected' : '' ?>>Useful assistant</option>
              <option value="4" <?= chat::coding_assistant == $dto->assistant ? 'selected' : '' ?>>Coding assistant</option>
            </select>
            <label for="<?= $_uid ?>">assistant type</label>
          </div>
        </div>
        <div class="modal-footer">
          <div class="js-message"></div>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    (_ => {
      const form = $('#<?= $_form ?>');
      const modal = $('#<?= $_modal ?>');

      const msg = txt => {

        const ctl = modal.find('.js-message').html(txt);
        ctl[0].className = 'me-auto js-message small p-2';
        return ctl;
      };

      const alert = txt => msg(txt).addClass('alert alert-warning');

      modal.on('shown.bs.modal', () => {

        form
          .on('submit', function(e) {
            // const _data = $(this).serializeFormJSON();
            _.fetch.post.form(_.url('<?= $this->route ?>'), this)
              .then(d => {
                if ('ack' == d.response) {

                  modal.trigger('success');
                  modal.modal('hide');
                } else {

                  alert(d.description ?? d);
                }
              });

            // console.table( _data);
            return false;
          });

        form.find('input[name="name"]').focus();
      });
    })(_brayworth_);
  </script>
</form>