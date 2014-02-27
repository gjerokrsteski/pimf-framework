<?php
/**
 * Util
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
