<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Pimf\Util\String;

/**
 * String
 *
 * @package Util_String
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Sanitize
{

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function removeNullCharacters($string)
  {
    return preg_replace(array ('/\0+/', '/(\\\\0)+/'), '', $string);
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function validateStandardCharacterEntities($string)
  {
    return preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "\\1;", $string);
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function validateUTF16TwoByteEncoding($string)
  {
    return preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "\\1\\2;", $string);
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function strangeThingsAreSubmitted($string)
  {
    return preg_replace(array ("/%u0([a-z0-9]{3})/i", "/%([a-z0-9]{2})/i"), "&#x\\1;", $string);
  }

  /**
   * @param string $string  String to check
   * @param string $charset Character set (default ISO-8859-1)
   *
   * @return mixed
   */
  public static function convertCharacterEntitiesToASCII($string, $charset)
  {
    $matches = array();

    if (preg_match_all("/<(.+?)>/si", $string, $matches)) {

      $count = count($matches['0']);

      for ($i = 0; $i < $count; $i++) {
        $string = str_replace(
          $matches['1'][$i],
          html_entity_decode($matches['1'][$i], ENT_COMPAT, $charset),
          $string
        );
      }
    }

    return $string;
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function convertAllTabsToSpaces($string)
  {
    return preg_replace("#\t+#", " ", $string);
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function makesPhpTagsSafe($string)
  {
    return str_replace(
      array ('<?php', '<?PHP', '<?', '?>'),
      array ('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'),
      $string
    );
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function compactAnyExplodedWords($string)
  {
    $words = array ('javascript', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');
    foreach ($words as $word) {
      $temp = '';
      $len = strlen($word);
      for ($i = 0; $i < $len; $i++) {
        $temp .= substr($word, $i, 1) . "\s*";
      }
      $temp   = substr($temp, 0, -3);
      $string = preg_replace('#' . $temp . '#s', $word, $string);
      $string = preg_replace('#' . ucfirst($temp) . '#s', ucfirst($word), $string);
    }
    return $string;
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function removeDisallowedJavaScriptInLinksOrImgTags($string)
  {
    $string = preg_replace(
      "#<a.+?href=.*?(alert\(|alert&\#40;|javascript\:|window\.|document\.|\.cookie|<script|<xss).*?\>.*?</a>#si",
      "",
      $string
    );
    $string = preg_replace(
      "#<img.+?src=.*?(alert\(|alert&\#40;|javascript\:|window\.|document\.|\.cookie|<script|<xss).*?\>#si",
      "",
      $string
    );
    return preg_replace("#<(script|xss).*?\>#si", "", $string);
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function removeJavaScriptEventHandlers($string)
  {
    return preg_replace(
      '#(<[^>]+.*?)(onblur|onchange|onclick|onfocus|onload|onmouseover|onmouseup|'
      . 'onmousedown|onselect|onsubmit|onunload|onkeypress|onkeydown|onkeyup|onresize)[^>]*>#iU',
      "\\1>",
      $string
    );
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function healNaughtyHTMLElements($string)
  {
    return preg_replace(
      '#<(/*\s*)(alert|applet|basefont|base|behavior|bgsound|'
      . 'blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input'
      . '|layer|link|meta|object|plaintext|style|script|textarea|title|xml|xss)([^>]*)>#is',
      "&lt;\\1\\2\\3&gt;",
      $string
    );
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function healNaughtyScriptingElements($string)
  {
    return preg_replace(
      '#(alert|cmd|passthru|eval|exec|system|fopen|fsockopen|'
      . 'file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
      "\\1\\2&#40;\\3&#41;",
      $string
    );
  }

  /**
   * @param string $string String to check
   *
   * @return mixed
   */
  public static function removeJavaScriptHardRedirects($string)
  {
    $bad = array (
      'document.cookie' => '',
      'document.write'  => '',
      'window.location' => '',
      "javascript\s*:"  => '',
      "Redirect\s+302"  => '',
      '<!--'            => '&lt;!--',
      '-->'             => '--&gt;'
    );
    foreach ($bad as $key => $val) {
      $string = preg_replace("#" . $key . "#i", $val, $string);
    }
    return $string;
  }
}