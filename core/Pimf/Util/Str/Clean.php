<?php
/**
 * Util
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Pimf\Util\Str;

/**
 * String
 *
 * @package Util_String
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Clean
{
    /**
     * An aggressive cleaning - all tags and stuff inside will be removed.
     *
     * @param string $string The string.
     *
     * @return string
     */
    public static function aggressive($string)
    {
        return (string)preg_replace("/<.*?>/", "", (string)$string);
    }

    /**
     * Cleans against XSS.
     *
     * @param string $string  String to check
     * @param string $charset Character set (default ISO-8859-1)
     *
     * @return string $value Sanitized string
     */
    public static function xss($string, $charset = 'ISO-8859-1')
    {
        $sanitize = new Sanitize();

        $string = $sanitize::removeNullCharacters($string);
        $string = $sanitize::validateStandardCharacterEntities($string);
        $string = $sanitize::validateUTF16TwoByteEncoding($string);
        $string = $sanitize::strangeThingsAreSubmitted($string);
        $string = $sanitize::convertCharacterEntitiesToASCII($string, $charset);
        $string = $sanitize::convertAllTabsToSpaces($string);
        $string = $sanitize::makesPhpTagsSafe($string);
        $string = $sanitize::compactAnyExplodedWords($string);
        $string = $sanitize::removeDisallowedJavaScriptInLinksOrImgTags($string);
        $string = $sanitize::removeJavaScriptEventHandlers($string);
        $string = $sanitize::healNaughtyHTMLElements($string);
        $string = $sanitize::healNaughtyScriptingElements($string);
        $string = $sanitize::removeJavaScriptHardRedirects($string);

        return $string;
    }
}
