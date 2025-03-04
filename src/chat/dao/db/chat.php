<?php
// file: src/chat/dao/db/chat.php

$dbc = \sys::dbCheck('chat');

/**
 * note:
 *  id, autoincrement primary key is added to all tables - no need to specify
 *  field types are MySQL and are converted to SQLite equivalents as required
 */

$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->defineField('name', 'varchar');

$dbc->check();  // actually do the work, check that table and fields exist