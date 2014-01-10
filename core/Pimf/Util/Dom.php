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

namespace Pimf\Util;

/**
 * A powerful tool for HTML or XML document manipulation and extraction of data.
 *
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Dom extends \DOMDocument
{
  /**
   * Retries a list of attributes-value which can live inside a HTML/XML-Element
   *
   * @param string $tag A HTML/XML tag-name representation for the HTML/XML-Element
   * @param string $attribute A attribute inside of the HTML/XML-Element
   *
   * @return array
   */
  public function fetchValues($tag, $attribute)
  {
    $values = array();

    // loop through each tag in the dom and add it to the array
    foreach ($this->getElementsByTagName($tag) as $tag)
    {
      /* @var $tag \DOMElement */
      $values[] = $tag->getAttribute($attribute);
    }

    return $values;
  }

  /**
   * Grab all links in a page
   * @return array
   */
  public function getURLs()
  {
    return $this->fetchValues('a', 'href');
  }

  /**
   * Grab all URLs of an image
   * @return array
   */
  public function getImageURLs()
  {
    return $this->fetchValues('img', 'src');
  }

  /**
   * Grab all URLs of an external script file like JS
   */
  public function getScriptURLs()
  {
    return $this->fetchValues('script', 'src');
  }

  /**
   * Grab all URLs location of the linked document like CSS
   */
  public function getCssURLs()
  {
    return $this->fetchValues('link', 'href');
  }
}
