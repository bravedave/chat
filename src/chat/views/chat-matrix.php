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

use strings;

?>
<div class="accordion" id="<?= $_uidAccordion = strings::rand() ?>">

  <div class="accordion-item border-0">

    <div id="<?= $_uidAccordion ?>-feed" class="accordion-collapse collapse show" data-bs-parent="#<?= $_uidAccordion ?>">

      <div class="row gx-2 mb-2 d-print-none">

        <div class="col">
          <input type="search" accesskey="/" class="form-control" id="<?= $_search = strings::rand() ?>" autofocus>
        </div>
        <div class="col-auto">
          <button type="button" data-id="0" class="btn btn-outline-secondary js-add">
            <i class="bi bi-plus"></i>
          </button>
        </div>
      </div>

      <div class="table-responsive">

        <table class="table table-sm" id="<?= $_table = strings::rand() ?>">

          <thead class="small">
            <tr>
              <td class="text-center js-line-number"></td>
              <td>name</td>
              <td class="text-center">updated</td>
              <td class="text-center">lines</td>
            </tr>
          </thead>

          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="accordion-item border-0">
    <div id="<?= $_uidAccordion ?>-workbench" class="accordion-collapse collapse" data-bs-parent="#<?= $_uidAccordion ?>">
      <nav class="navbar navbar-expand d-print-none">
        <div class="navbar-brand">Workbench</div>
        <nav class="navbar-nav ms-auto">
          <button type="button" class="btn-close ms-2" data-bs-toggle="collapse" data-bs-target="#<?= $_uidAccordion ?>-feed"></button>
        </nav>
      </nav>
    </div>
  </div>
</div>
<script>
  (_ => {
    const feed = $('#<?= $_uidAccordion ?>-feed');
    const workbench = $('#<?= $_uidAccordion ?>-workbench');
    const table = $('#<?= $_table ?>');
    const search = $('#<?= $_search ?>');

    const getMatrix = () => new Promise((resolve, reject) => {

      table.placeholders();

      _.fetch.post(_.url('<?= $this->route ?>'), {
        action: 'chat-get-matrix'
      }).then(d => 'ack' == d.response ? resolve(d.data) : reject(d));
    });

    const matrix = data => {

      table.clearPlaceholders();
      const tbody = table.find('tbody').empty();

      $.each(data, (i, dto) => tbody.append(matrixRow(dto)));
      // console.log(data);

      table.trigger('update-line-numbers');
    };

    const matrixNewChat = function(e) {

      const tabs = _.tabs(workbench);
      const view = tabs.newTab('view');

      view.tab.on('show.bs.tab', e => {

        view.pane
          .addClass('pt-2')
          .empty()
          .load(_.url(`<?= $this->route ?>/view/0`));
      });

      view.pane.on('chat-updated', (e, chat) => {

        console.log(chat);
        tabs.nav.find('h5').text(chat.name);
        getMatrix()
          .then(data => {

            matrix(data);
            const tr = table.find(`tr[data-id="${chat.id}"]`);
            if (tr.length > 0) tr.addClass('table-active');
          }).catch(_.growl);
      });

      tabs.nav.prepend(`<h5 class="me-auto mt-2">New Chat</h5>`);
      tabs.nav.append(`<button type="button" class="btn-close mt-2 ms-2" data-bs-toggle="collapse"
    data-bs-target="#<?= $_uidAccordion ?>-feed"
    aria-expanded="false" aria-controls="<?= $_uidAccordion ?>-feed"></button>`);

      workbench.collapse('show');
      view.tab.tab('show');
    };

    const matrixRow = dto => {

      // console.log(dto);

      const tr = $(`<tr class="pointer" data-id="${dto.id}">
          <td class="text-center js-line-number" />
          <td class="js-name">${dto.name}</td>
          <td class="text-center js-updated">${_.asLocaleDate(dto.updated)}</td>
          <td class="text-center js-count">${dto.linecount}</td>
        </tr>`)
        .data('dto', dto)
        .on('click', matrixRowClick)
        .on('contextmenu', matrixRowContext)
        .on('delete', matrixRowDelete)
        .on('edit', matrixRowEdit)
        .on('refresh', matrixRowRefresh)
        .on('view', matrixRowView);

      return tr;
    };

    const matrixRowClick = function(e) {

      _.hideContexts(e);
      $(this).trigger('view');
    }

    const matrixRowContext = function(e) {

      if (e.shiftKey) return;
      let _ctx = _.context(e); // hides any open contexts and stops bubbling

      _ctx.append.a({
        html: '<striong>view</strong>',
        click: e => $(this).trigger('view')
      });

      _ctx.append.a({
        html: '<i class="bi bi-pencil"></i>edit',
        click: e => $(this).trigger('edit')
      });

      _ctx.append.a({
        html: '<i class="bi bi-trash"></i>delete',
        click: e => $(this).trigger('delete')
      });

      _ctx.append.a({
        html: '<i class="bi bi-arrow-clockwise"></i>refresh',
        click: e => $(this).trigger('refresh')
      });

      _ctx.append('<hr>');
      _ctx.append.a({
        html: 'dump',
        click: e => console.log(this.dataset)
      });

      _ctx.open(e);
    };

    const matrixRowEdit = function(e) {

      _.hideContexts(e);

      _.get.modal(_.url(`<?= $this->route ?>/edit/${this.dataset.id}`))
        .then(modal => modal.on('success', (e, result) => $(this).trigger('refresh')));
    };

    const matrixRowDelete = function(e) {

      _.hideContexts(e);

      _.ask.alert.confirm('Are you sure ?')
        .then(() => {

          _.fetch.post(_.url('<?= $this->route ?>'), {
            action: 'chat-delete',
            id: this.dataset.id
          }).then(d => {

            if ('ack' == d.response) {

              this.remove();
              table.trigger('update-line-numbers');
            } else {

              _.growl(d);
            }
          });
        });
    };

    const matrixRowRefresh = function(e) {

      const tr = $(this);

      _.fetch
        .post(_.url('<?= $this->route ?>'), {
          action: 'chat-get-by-id',
          id: this.dataset.id
        })
        .then(d => {
          if ('ack' == d.response) {

            const dto = d.data;
            tr.find('.js-name').text(dto.name);
            tr.find('.js-updated').text(_.asLocaleDate(dto.updated));
            tr.find('.js-count').text(dto.lines.length);
            tr.data('dto', dto)

            console.log(dto);
          } else {
            _.growl(d);
          }
        });
    }

    const matrixRowView = function(e) {

      const $this = $(this);
      $this.addClass('table-active');
      const dto = $this.data('dto');

      const tabs = _.tabs(workbench);
      const view = tabs.newTab('view');

      view.tab.on('show.bs.tab', e => {

        view.pane
          .addClass('pt-2')
          .empty()
          .load(_.url(`<?= $this->route ?>/view/${this.dataset.id}`));
      });

      tabs.nav.prepend(`<h5 class="me-auto mt-2">${dto.name}</h5>`);
      tabs.nav.append(`<button type="button" class="btn-close mt-2 ms-2" data-bs-toggle="collapse"
          data-bs-target="#<?= $_uidAccordion ?>-feed"
          aria-expanded="false" aria-controls="<?= $_uidAccordion ?>-feed"></button>`);

      workbench.collapse('show');
      view.tab.tab('show');
    };

    feed.find('.js-add').on('click', e => {

      e.stopPropagation();
      matrixNewChat();
      // _.get.modal(_.url('<?= $this->route ?>/edit'))
      //   .then(modal => modal.on('success', (e, result) => {

      //     getMatrix().then(matrix).catch(_.growl);
      //   }));
    });

    table.on('update-line-numbers', _.table._line_numbers_);
    // return true from the prefilter to show the row
    _.table.search(search, table, /* prefilter tr => true */ );

    [
      feed,
      workbench,
    ].forEach(el => {
      el
        .on('hide.bs.collapse', e => e.stopPropagation())
        .on('hidden.bs.collapse', e => e.stopPropagation())
        .on('show.bs.collapse', e => e.stopPropagation())
        .on('shown.bs.collapse', e => e.stopPropagation());
    });

    feed.on('show.bs.collapse', e => $('body').toggleClass('hide-nav-bar', false));
    feed.on('shown.bs.collapse', e => {

      const active = table.find('tbody tr.table-active');

      if (active.length > 0) {

        active[0].scrollIntoView({
          block: "center"
        });
        setTimeout(() => active.removeClass('table-active'), 800);
      }
    });

    workbench.on('show.bs.collapse', e => $('body').toggleClass('hide-nav-bar', true));

    _.ready(() => getMatrix().then(matrix).catch(_.growl));
  })(_brayworth_);
</script>