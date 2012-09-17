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
 * PHP Memory Usage Information
 *
 * A util class, which can be used to collect the PHP script memory usage information
 * and to print all information.
 *
 * <code>
 * // Create new MemoryUsageInformation class
 * $memoryUsage = new Pimf_Util_MemoryUsage(true);
 * // Set start
 * $memoryUsage->setStart();
 * // Set memory usage before loop
 * $memoryUsage->setMark('Before Loop');
 *
 *    // Create example array
 *    $a = array();
 *
 *    // Fill array with
 *    for($i = 0; $i < 100000; $i++) {
 *       $a[$i] = uniqid();
 *    }
 *
 * // Set memory usage after loop
 * $memoryUsage->setMark('After Loop');
 *
 *    // Unset array
 *    unset($a);
 *
 * // Set memory usage after unset
 * $memoryUsage->setMark('After Unset');
 * // Set end
 * $memoryUsage->setEnd();
 * // Print memory usage statistics
 * $memoryUsage->printInformation();
 *
 * ...
 * ..
 * .
 *
 *  // Example Output of Memory Usage Information class.
 *  Time: 1334563829 | Memory Usage: 512.00 KB | Info: Initial Memory Usage
 *  Time: 1334563829 | Memory Usage: 512.00 KB | Info: Before Loop
 *  Time: 1334563829 | Memory Usage: 17.25 MB | Info: After Loop
 *  Time: 1334563829 | Memory Usage: 1.00 MB | Info: After Unset
 *  Time: 1334563829 | Memory Usage: 1.00 MB | Info: Memory Usage at the End
 *
 * Peak of memory usage: 17.25 MB
 * </code>
 *
 * @package Pimf_Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Util_MemoryUsage
{
  /**
   * Set this to TRUE to get the real size of memory allocated from system.
   * If not set or FALSE only the memory used by emalloc() is reported.
   *
   * @var bool
   */
  private $realUsage;

  /**
   * @var array
   */
  private $statistics = array();

  /**
   * Memory Usage Information constructor.
   *
   * @param bool $realUsage (Optional) The real size of memory allocated from system.
   */
  public function __construct($realUsage = false)
  {
    $this->realUsage = $realUsage;
  }

  /**
   * Returns current memory usage with or without styling.
   *
   * @param bool $withStyle (Optional) If use Byte formatting.
   * @return int|string
   */
  public function getCurrent($withStyle = true)
  {
    $mem = memory_get_usage($this->realUsage);
    return ($withStyle) ? $this->byteFormat($mem) : $mem;
  }

  /**
   * Returns peak of memory usage.
   *
   * @param bool $withStyle (Optional) If use Byte formatting.
   * @return int|string
   */
  public function getPeak($withStyle = true)
  {
    $mem = memory_get_peak_usage($this->realUsage);
    return ($withStyle) ? $this->byteFormat($mem) : $mem;
  }

  /**
   * Mark the point of the memory usage with info.
   *
   * @param string $info (Optional) The mark information.
   */
  public function setMark($info = '')
  {
    $this->statistics[] = array(
      'time'         => time(),
      'info'         => $info,
      'memory_usage' => $this->getCurrent()
    );
  }

  /**
   * Print all memory usage info and memory limit.
   */
  public function printInformation()
  {
    foreach ($this->statistics as $satistic) {
      echo  "Time: " . $satistic['time']
        . " | Memory Usage: " . $satistic['memory_usage']
        . " | Info: " . $satistic['info'];
      echo "\n";
    }

    echo "\n\n";
    echo "Peak of memory usage: " . $this->getPeak();
    echo "\n\n";
  }

  /**
   * Set start with default info or some custom info.
   *
   * @param string $info (Optional) The mark information.
   */
  public function setStart($info = 'Initial Memory Usage')
  {
    $this->setMark($info);
  }

  /**
   * Set end with default info or some custom info.
   *
   * @param string $info (Optional) The mark information.
   */
  public function setEnd($info = 'Memory Usage at the End')
  {
    $this->setMark($info);
  }

  /**
   * Byte formatting
   *
   * @param $bytes
   * @param string $unit (Optional) prefix by bytes.
   * @param int $decimals (Optional) format for decimals.
   * @return string
   */
  private function byteFormat($bytes, $unit = "", $decimals = 2)
  {
    $units = array(
      'B'  => 0,
      'KB' => 1,
      'MB' => 2,
      'GB' => 3,
      'TB' => 4,
      'PB' => 5,
      'EB' => 6,
      'ZB' => 7,
      'YB' => 8
    );

    $value = 0;
    if ($bytes > 0) {
      // Generate automatic prefix by bytes
      // If wrong prefix given
      if (!array_key_exists($unit, $units)) {
        $pow  = floor(log($bytes) / log(1024));
        $unit = array_search($pow, $units);
      }

      // Calculate byte value by prefix
      $value = ($bytes / pow(1024, floor($units[$unit])));
    }

    // If decimals is not numeric or decimals is less than 0
    // then set default value
    if (!is_numeric($decimals) || $decimals < 0) {
      $decimals = 2;
    }

    // Format output
    return sprintf('%.' . $decimals . 'f ' . $unit, $value);
  }
}