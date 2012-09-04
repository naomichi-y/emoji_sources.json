<?php
define('UNICODE_EMOJI_PATH', 'http://unicode.org/Public/UNIDATA/EmojiSources.txt');
define('JSON_WRITE_PATH', 'EmojiSources.json');
 
$contents = file_get_contents(UNICODE_EMOJI_PATH);
$pattern = '/^([0-9A-F\s]+);([0-9A-F]+)?;([0-9A-F]+)?;([0-9A-F]+)?$/m';
$emojiList = array();
 
if (preg_match_all($pattern, $contents, $matches)) {
  $j = sizeof($matches[1]);
 
  for ($i = 0; $i < $j; $i++) {
    $unicode = trim($matches[1][$i]);
 
    if (strpos($unicode, ' ') !== FALSE) {
      $array = explode(' ', $unicode);
      $utf8hex = unicode_to_utf8($array[0]) . unicode_to_utf8($array[1]);
 
    } else {
      $utf8hex = unicode_to_utf8($unicode);
    }
 
    $map = array();
    $map['unicode'] = $unicode;
    $map['utf8hex'] = $utf8hex;
    $map['sjis_docomo'] = $matches[2][$i];
    $map['sjis_kddi'] = $matches[3][$i];
    $map['sjis_softbank'] = $matches[4][$i];
 
    $emojiList[] = $map;
  }
 
  $jsonData = json_encode($emojiList);
  file_put_contents(JSON_WRITE_PATH, $jsonData);
 
  printf("Created a file. [%s]\n", JSON_WRITE_PATH);
 
} else {
  printf("[ERROR] Failed parse file. [%s]\n", UNICODE_EMOJI_PATH);
}
 
/**
* @param string code
* @return string
* @link http://en.wikipedia.org/wiki/UTF-8#Description
*/
function unicode_to_utf8($code)
{
  $code = hexdec($code);
  $value = FALSE;
 
  if ($code <= 0x7F) {
    $value = sprintf('\x%x', $code);
 
  } else if ($code <= 0x7FF) {
    $value = sprintf("\x%x\x%x",
      (($code >> 6) + 192),
      (($code & 63) + 128));
 
  } else if ($code <= 0xFFFF) {
    $value = sprintf("\x%x\x%x\x%x",
      (($code >> 12) + 224),
      ((($code >> 6) & 63) + 128),
      (($code & 63) + 128));
 
  } else if ($code <= 0x1FFFFF) {
    $value = sprintf("\x%x\x%x\x%x\x%x",
      ($code >> 18) + 240,
      (($code >> 12) & 63) + 128,
      ((($code >> 6) & 63) + 128),
      (($code & 63) + 128));
  }
 
  return $value;
}
