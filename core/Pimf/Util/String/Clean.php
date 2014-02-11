<?php
/**
 * Util
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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf\Util\String;

/**
 * String
 *
 * @package Util_String
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Clean
{
  /**
   * An aggressive cleaning - all tags and stuff inside will be removed.
   *
   * @param string $string The string.
   * @return string|boolean
   */
  public static function aggressive($string)
  {
    return (string) preg_replace("/<.*?>/", "", (string)$string);
  }

  /**
   * Cleans against XSS.
   * @param string $string String to check
   * @param string $charset Character set (default ISO-8859-1)
   * @return string|bool $value Sanitized string
   */
  public static function xss($string, $charset = 'ISO-8859-1')
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
    * Add a semicolon if missing.  Enables the conversion of entities to ASCII later.
    */
    $string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "\\1;", $string);

   /*
    * Validate UTF16 two byte encoding (x00)
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
    * Note: XML tags are inadvertently replaced too: <?xml
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
    * the event handler and anything up to the closing >
    */
    $string = preg_replace(
      '#(<[^>]+.*?)(onblur|onchange|onclick|onfocus|onload|onmouseover|onmouseup|'.
      'onmousedown|onselect|onsubmit|onunload|onkeypress|onkeydown|onkeyup|onresize)[^>]*>#iU', "\\1>", $string);

   /*
    * Sanitize naughty HTML elements
    * If a tag containing any of the words in the list
    * below is found, the tag gets converted to entities.
    * So this: <blink>
    * Becomes: &lt;blink&gt;
    */
    $string = preg_replace('#<(/*\s*)(alert|applet|basefont|base|behavior|bgsound|'.
      'blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input'.
      '|layer|link|meta|object|plaintext|style|script|textarea|title|xml|xss)([^>]*)>#is', "&lt;\\1\\2\\3&gt;", $string);

   /*
    * Sanitize naughty scripting elements
    * For example:	eval('some code')
    * Becomes:		eval&#40;'some code'&#41;
    */
    $string = preg_replace('#(alert|cmd|passthru|eval|exec|system|fopen|fsockopen|'.
      'file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $string);

   /*
    * Final clean up
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
}
 