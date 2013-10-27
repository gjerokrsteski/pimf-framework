<?php
/**
 * Pimf_Util
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * Pimf_Util_String
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_String
{
  /**
   * Check if a string is UTF-8 encoded.
   *
   * @param string $string The string.
   * @param boolean $useBuiltinCheck (Optional) Check if the string is valid utf-8 string.
   * @return boolean
   */
  public static function isUTF8($string, $useBuiltinCheck = false)
  {
    if ($useBuiltinCheck === true && function_exists('mb_check_encoding')) {
      return mb_check_encoding($string, 'UTF-8');
    }

    $length = strlen($string);

    for ($i = 0; $i < $length; ++$i) {

      if (ord($string[$i]) < 0x80) {
        $n = 0;
      } elseif ((ord($string[$i]) & 0xE0) == 0xC0) {
        $n = 1;
      } elseif ((ord($string[$i]) & 0xF0) == 0xE0) {
        $n = 2;
      } elseif ((ord($string[$i]) & 0xF0) == 0xF0) {
        $n = 3;
      } else {
        return false;
      }

      for ($j = 0; $j < $n; $j++) {
        if ((++$i == $length) || ((ord($string[$i]) & 0xC0) != 0x80)) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Replace special chars by underscores.
   * @param $value
   * @param string $replaceWith
   * @return mixed
   */
  public static function slagSpecialChars($value, $replaceWith = '_')
  {
    return preg_replace(array("/[^a-zA-Z0-9]/", "/$replaceWith+/", "/$replaceWith$/"), array("_", "_", ""), $value);
  }

  /**
   * Check value to find if it was serialized.
   *
   * If $data is not an string, then returned value will always be false.
   * Serialized data is always a string.
   *
   * @param   mixed  $data  Value to check to see if was serialized
   * @return  bool
   */
  public static function isSerialized($data)
  {
    // If it isn't a string, it isn't serialized
    if ($data !== (string)$data) {
      return false;
    }

    $data = trim($data);

    if ('N;' == $data) {
      return true;
    }

    $length = strlen($data);

    if ($length < 4) {
      return false;
    }

    if (':' !== $data[1]) {
      return false;
    }

    $lastChar = $data[$length - 1];

    if (';' !== $lastChar && '}' !== $lastChar) {
      return false;
    }

    $token = $data[0];

    switch ($token) {

      case 's':

        if ('"' !== $data[$length - 2]) {
          return false;
        }

        return true;

      case 'a' :
      case 'O' :
        return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
      case 'b' :
      case 'i' :
      case 'd' :
        return (bool)preg_match("/^{$token}:[0-9.E-]+;\$/", $data);
    }

    return false;
  }

  /**
   * Check for invalid UTF8 encoding and invalid byte .
   *
   * @param string $string Your string.
   * @return boolean
   */
  public static function checkUtf8Encoding($string)
  {
    if (!mb_check_encoding($string, 'UTF-8')) {
      return false;
    }

    if (!$string == mb_convert_encoding(mb_convert_encoding($string, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')) {
      return false;
    }

    return true;
  }

  /**
   * Function truncates a HTML string, preserving and closing all tags.
   *
   * @param string $str The string.
   * @param integer $textMaxLength Max length.
   * @param string $postString (Optional) Post string for the concatenation after the cutting.
   * @param string $encoding (Optional) Which encoding - default is utf-8.
   * @return string
   */
  public static function truncatePreservingTags($str, $textMaxLength, $postString = '...', $encoding = 'UTF-8')
  {
    // Complete string length with tags
    $htmlLength = mb_strlen($str, $encoding);
    // Cursor position
    $htmlPos = 0;
    // Extracted text length without tags
    $textLength = 0;
    // Tags that need to closed
    $closeTag = array();

    if ($htmlLength <= $textMaxLength) {
      return $str;
    }

    // Loop through str, start at cursor position
    for ($i = $htmlPos; $i < $htmlLength; $i++) {

      // Extract multibyte encoded char on cursor position
      $char = mb_substr($str, $i, 1, $encoding);

      // Check on an angle bracket assuming text following is a tag
      if ($char == '<') {
        // Sub cursor position inside the tag (after the first angle bracket)
        $tagPos = $i +1;
        // Remember first position inside the tag
        $tagFirst = $tagPos;
        // Size of tag (including angle brackets <1234>)
        $tagSize = 1;
        // Tag name
        $tagName = '';

        // Tag is valid ( <tag> OR </tag> )
        $isTag = true;
        // Tag is a closing tag </tag>
        $isClosingTag = false;

        // Loop through text inside the tag until it is closed
        for ($j = $tagPos; $j < $htmlLength; $j++) {

          // Extract multibyte encoded char on sub cursor position
          $charTag = mb_substr($str, $j, 1, $encoding);

          // Another opening angle bracket = first angle bracket serves as "smaller as"
          // Not a valid tag, set mark and break
          if ($charTag === '<') {
            $isTag = false;
            break;
          }
          // Seems to be an '<>' entity
          // Not a valid tag, set mark and break
          elseif ($tagFirst == $j && $charTag === '>') {
            $isTag = false;
            break;
          }
          // Closing tag, set mark
          elseif ($tagFirst == $j && $charTag === '/') {
            $isClosingTag = true;
          }
          // Tag has ended, break
          elseif ($charTag === '>') {
            break;
          }
          // Remember char as tag name
          else {
            $tagName .= $charTag;
          }

          $tagSize++;
        }

        // Valid tag
        if (!empty($isTag)) {
          // Closing tag </tag>
          if (!empty($isClosingTag)) {
            // Remove it from closing array
            array_pop($closeTag);
          }
          // Not a closing tag
          else {
            // Remember to close it
            $closeTag[] = $tagName;
          }

          // Set cursor position, continue with next char after the tag
          $i = $i + $tagSize;
        }
        // Not a valid tag
        else {
          // Set assumed tag size as additional textLength, because it is text
          $textLength = $textLength + $tagSize;
          // Set cursor position and reset the last < or > char to start a new check
          $i = $i + $tagSize -1;
        }
      }
      // Normal char, increase textLength
      else {
        $textLength++;
      }

      // If maximum length reached break
      if ($textLength >= $textMaxLength) {
        break;
      }
    }

    // Set cursor to new position
    $htmlPos = $i + 1;

    // Extract preview text from 0 to current cursor position
    $ret = mb_substr($str, 0, $htmlPos, $encoding);

    // If necessary set placeholder string (before tags get closed)
    if ($textLength >= $textMaxLength) {
      $ret .= $postString;
    }

    // If we are between tags, close them correctly
    if (count($closeTag)) {
      foreach (array_reverse($closeTag) as $tag) {
        $tokens = explode(' ', $tag);
        $ret .= "</" . $tokens[0] . ">";
      }
    }

    return $ret;
  }

  /**
   * Ensure that a string is ends with a special string.
   *
   * <code>
   * - ensureTrailing('/', 'http://www.example.com') -> 'http://www.example.com/'
   * - ensureTrailing('/', 'http://www.example.com/') -> 'http://www.example.com/'
   * </code>
   *
   * @param string $needle The needle.
   * @param string $haystack The haystack.
   *
   * @return string
   */
  public static function ensureTrailing($needle, $haystack)
  {
    $needleLength = strlen($needle);
    $needlePart   = substr($haystack, -1 * $needleLength);

    if ($needlePart !== $needle) {
      // append missing trailing character.
      $haystack .= $needle;
    }

    return $haystack;
  }

  /**
   * Ensure that a string is starts with a special string.
   *
   * <code>
   * - ensureLeading('#', '1#2#3#4#5') -> '#1#2#3#4#5'
   * - ensureLeading('#', '#1#2#3#4#5') -> '#1#2#3#4#5'
   * </code>
   *
   * @param string $needle The needle.
   * @param string $haystack The haystack
   * @return string
   */
  public static function ensureLeading($needle, $haystack)
  {
    $needleLength = strlen($needle);
    $needlePart   = substr($haystack, 0, $needleLength);

    if ($needlePart !== $needle) {
      // append missing trailing string
      $haystack = $needle . $haystack;
    }

    return $haystack;
  }

  /**
   * Delete trailing characters.
   *
   * <code>
   * - deleteTrailing('|', '|1|2|3|4|5|')               -> '|1|2|3|4|5'
   * - deleteTrailing(array('|','5'), '|1|2|3|4|5|555') -> '|1|2|3|4'
   * </code>
   *
   * @param string|array $needle The needle.
   * @param string $haystack The haystack.
   * @return mixed
   */
  public static function deleteTrailing($needle, $haystack)
  {
    $pattern = '#(' . self::pregQuote($needle, '#') . ')+$#';
    $result  = preg_replace($pattern, '', $haystack);

    return $result;
  }

  /**
   * Delete leading characters.
   *
   * <code>
   * - deleteTrailing('#', '#1#2#3#4#5')             -> '1#2#3#4#5'
   * - deleteTrailing(array('#', '1'), '##11#2#3#4#5') -> '2#3#4#5'
   * </code>
   *
   * @param string|array $needle The needle.
   * @param string $haystack The haystack.
   * @return mixed
   */
  public static function deleteLeading($needle, $haystack)
  {
    $pattern = '#^(' . self::pregQuote($needle, '#') . ')+#';
    $result  = preg_replace($pattern, '', $haystack);

    return $result;
  }

  /**
   * Wrapper for preg_quote supporting strings and array of strings.
   *
   * @param mixed $values The values.
   * @param null $delimiter (Optional) The delimiter.
   * @return string
   */
  public static function pregQuote($values, $delimiter = null)
  {
    if (!is_array($values)) {
      return preg_quote($values, $delimiter);
    }

    // Case: needle is array
    foreach ($values as $key => $value) {
      $values[$key] = preg_quote($value, $delimiter);
    }

    return implode('|', $values);
  }

  /**
   * An aggressive cleaning - all tags and stuff inside will be removed.
   *
   * @param string $text The string.
   * @return string|boolean
   */
  public static function cleanAggressive($text)
  {
    return (string) preg_replace("/<.*?>/", "", (string) $text);
  }

  /**
   * Tries to clean up the string from html and stuff but not to clean usefully information.
   *
   * @param string $string The string.
   * @return string
   */
  public static function cleanSmart($string)
  {
    $string = trim($string);

    if (!$string) {
      return '';
    }

    // Multiline tags: head/script/style
    $search = array(
      '@<head[^>]*?>.*?</head>@si',
      '@<script[^>]*?>.*?</script>@si',
      '@<style[^>]*?>.*?</style>@si'
    );

    $cleanString = preg_replace($search, '', $string);

    // Do we have any interesting attributes in the tags?
    @preg_match_all('/\s(alt|title|src)="([^"]*)"/i', $cleanString, $imgLabels);

    $cOut = '';

    if (array_keys($imgLabels[1], "alt")) {
      $altList = array_keys($imgLabels[1], "alt");
      $cOut .= ' ' . html_entity_decode(
        $imgLabels[2][(int) array_shift($altList)], ENT_NOQUOTES, "UTF-8"
      );
    }

    if (array_keys($imgLabels[1], "title")) {
      $titleList = array_keys($imgLabels[1], "title");
      $cOut .= ' ' . html_entity_decode(
        $imgLabels[2][(int) array_shift($titleList)], ENT_NOQUOTES, "UTF-8"
      );
    }

    if (array_keys($imgLabels[1], "src")) {
      $srcList = array_keys($imgLabels[1], "src");
      $cOut .= ' ' . $imgLabels[2][(int) array_shift($srcList)];
    }

    return ($cOut !== '') ? $cOut : '';
  }

  /**
   * Cleans against XSS.
   * Info: use it on showing your request data.
   * @param string $string String to check
   * @param string $charset Character set (default ISO-8859-1)
   * @return string|bool $value Sanitized string
   */
  public static function cleanXss($string, $charset = 'ISO-8859-1')
  {
   /*
    * Remove Null Characters
    * This prevents sandwiching null characters
    * between ascii characters, like Java\0script.
    */
    $string = preg_replace('/\0+/', '', $string);
    $string = preg_replace('/(\\\\0)+/', '', $string);

   /*
    * Validate standard character entities
    * Add a semicolon if missing.  We do this to enable
    * the conversion of entities to ASCII later.
    */
    $string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "\\1;", $string);

   /*
    * Validate UTF16 two byte encoding (x00)
    * Just as above, adds a semicolon if missing.
    */
    $string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "\\1\\2;", $string);

   /*
    * URL Decode
    * Just in case stuff like this is submitted:
    * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
    * Note: Normally urldecode() would be easier but it removes plus signs
    */
    $string = preg_replace("/%u0([a-z0-9]{3})/i", "&#x\\1;", $string);
    $string = preg_replace("/%([a-z0-9]{2})/i", "&#x\\1;", $string);

   /*
    * Convert character entities to ASCII
    * This permits our tests below to work reliably.
    * We only convert entities that are within tags since
    * these are the ones that will pose security problems.
    */
    if (preg_match_all("/<(.+?)>/si", $string, $matches)) {
      for ($i = 0; $i < count($matches['0']); $i++) {
        $string = str_replace(
          $matches['1'][$i], html_entity_decode($matches['1'][$i], ENT_COMPAT, $charset), $string
        );
      }
    }

   /*
    * Convert all tabs to spaces
    * This prevents strings like this: ja	vascript
    * Note: we deal with spaces between characters later.
    */
    $string = preg_replace("#\t+#", " ", $string);

   /*
    * Makes PHP tags safe
    * Note: XML tags are inadvertently replaced too:
    *	<?xml
    * But it doesn't seem to pose a problem.
    */
    $string = str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $string);

   /*
    * Compact any exploded words
    * This corrects words like:  j a v a s c r i p t
    * These words are compacted back to their correct state.
    */
    $words = array('javascript', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');

    foreach ($words as $word) {
      $temp = '';
      for ($i = 0; $i < strlen($word); $i++) {
        $temp .= substr($word, $i, 1) . "\s*";
      }

      $temp   = substr($temp, 0, -3);
      $string = preg_replace('#' . $temp . '#s', $word, $string);
      $string = preg_replace('#' . ucfirst($temp) . '#s', ucfirst($word), $string);
    }

   /*
    * Remove disallowed Javascript in links or img tags
    */
    $string = preg_replace("#<a.+?href=.*?(alert\(|alert&\#40;|javascript\:|window\.|document\.|\.cookie|<script|<xss).*?\>.*?</a>#si", "", $string);
    $string = preg_replace("#<img.+?src=.*?(alert\(|alert&\#40;|javascript\:|window\.|document\.|\.cookie|<script|<xss).*?\>#si", "", $string);
    $string = preg_replace("#<(script|xss).*?\>#si", "", $string);

   /*
    * Remove JavaScript Event Handlers
    * Note: This code is a little blunt.  It removes
    * the event handler and anything up to the closing >,
    * but it's unlikely to be a problem.
    */
    $string = preg_replace('#(<[^>]+.*?)(onblur|onchange|onclick|onfocus|onload|onmouseover|onmouseup|onmousedown|onselect|onsubmit|onunload|onkeypress|onkeydown|onkeyup|onresize)[^>]*>#iU', "\\1>", $string);

   /*
    * Sanitize naughty HTML elements
    *
    * If a tag containing any of the words in the list
    * below is found, the tag gets converted to entities.
    * So this: <blink>
    * Becomes: &lt;blink&gt;
    */
    $string = preg_replace('#<(/*\s*)(alert|applet|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|layer|link|meta|object|plaintext|style|script|textarea|title|xml|xss)([^>]*)>#is', "&lt;\\1\\2\\3&gt;", $string);

   /*
    * Sanitize naughty scripting elements
    *
    * Similar to above, only instead of looking for
    * tags it looks for PHP and JavaScript commands
    * that are disallowed.  Rather than removing the
    * code, it simply converts the parenthesis to entities
    * rendering the code un-executable.
    *
    * For example:	eval('some code')
    * Becomes:		eval&#40;'some code'&#41;
    */
    $string = preg_replace('#(alert|cmd|passthru|eval|exec|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $string);

   /*
    * Final clean up
    *
    * This adds a bit of extra precaution in case
    * something got through the above filters
    */
    $bad = array(
      'document.cookie'  => '',
      'document.write'   => '',
      'window.location'  => '',
      "javascript\s*:"   => '',
      "Redirect\s+302"   => '',
      '<!--'             => '&lt;!--',
      '-->'              => '--&gt;'
    );

    foreach ($bad as $key => $val) {
      $string = preg_replace("#" . $key . "#i", $val, $string);
    }

    return $string;
  }

  /**
   * @param int $length
   * @return string
   */
  public static function random($length = 32)
  {
    return substr(
      str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, $length
    );
  }

  /**
   * Determine if a given string contains a given sub-string.
   *
   * @param string $haystack
   * @param string|array $needle
   * @return bool
   */
  public static function contains($haystack, $needle)
  {
    foreach ((array)$needle as $n) {
      if (strpos($haystack, $n) !== false) {
        return true;
      }
    }

    return false;
  }

  /**
   * Determine if a given string begins with a given value.
   *
   * @param string $haystack
   * @param string $needle
   * @return bool
   */
  public static function startsWith($haystack, $needle)
  {
  	return strpos($haystack, $needle) === 0;
  }

  /**
   * Determine if a given string ends with a given value.
   *
   * @param string $haystack
   * @param string $needle
   * @return bool
   */
  public static function endsWith($haystack, $needle)
  {
  	return $needle == substr($haystack, strlen($haystack) - strlen($needle));
  }
}
