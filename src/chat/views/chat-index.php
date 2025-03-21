<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\chat;

use bravedave\dvc\strings;

?>
<nav class="nav flex-column">
  <a class="h6" href="<?= strings::url($this->route) ?>"><?= config::label ?></a>
  <a class="nav-link" href="<?= strings::url($this->route . '/about') ?>">Readme.md</a>
</nav>