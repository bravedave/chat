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

use League\CommonMark\GithubFlavoredMarkdownConverter;
use bravedave\dvc\strings as dvcStrings;

class strings extends dvcStrings {

  static function markdownToHtml($markdown, $options = []): string {

    $mdo = [
      'allow_unsafe_links' => $options['allow_unsafe_links'] ?? false,
      'html_input' => $options['html_input'] ?? 'strip'
    ];

    $converter = new GithubFlavoredMarkdownConverter($mdo);
    // logger::debug(sprintf('<%s> %s', $markdown, logger::caller()));

    $output = (string)$converter->convert($markdown);

    return $output;
  }
}
