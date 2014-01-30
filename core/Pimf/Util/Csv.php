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
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Csv
{
  /**
   * The optional delimiter parameter sets the field
   * delimiter (one character only).
   * @var string
   */
  protected $delimiter;

  /**
   * The optional enclosure parameter sets the field
   * enclosure (one character only).
   * @var string
   */
  protected $enclosure;

  /**
   * @param null|string $fieldDelimiter
   * @param null|string $fieldEnclosure
   * @see http://de2.php.net/fputcsv
   */
  public function __construct($fieldDelimiter = ';', $fieldEnclosure = null)
  {
    $this->delimiter = $fieldDelimiter;
    $this->enclosure = $fieldEnclosure;
  }

  /**
   * @param array $data
   * @return string
   */
  public function create(array $data)
  {
    $fp = fopen('php://temp', 'a');

    $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
    fputs($fp, $bom, null);

    foreach ($data as $row) {
      fputcsv($fp, $row, $this->delimiter, $this->enclosure);
    }

    rewind($fp);
    $contents = stream_get_contents($fp);
    fclose($fp);
    return $contents;
  }
}
