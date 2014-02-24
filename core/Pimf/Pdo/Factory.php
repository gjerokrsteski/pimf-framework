<?php
/**
 * Database
 *
 * PHP Version 5
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

namespace Pimf\Pdo;

/**
 * Creates a PDO connection from the farm of connectors.
 *
 * @package Database
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Factory
{
  /**
   * @param array $config
   * @return \Pimf\Database
   * @throws \RuntimeException If no driver specified or no PDO installed.
   * @throws \UnexpectedValueException
   */
  public static function get(array $config)
  {
    if (!isset($config['driver']) or !$config['driver']) {
      throw new \RuntimeException('no driver specified');
    }

    $driver = strtolower($config['driver']);

    if (!in_array($driver, array('sqlite', 'mysql', 'sqlserver', 'postgre'), true)) {
      throw new \UnexpectedValueException('PDO driver "'.$driver.'" not supported by PIMF');
    }

    if(!extension_loaded('pdo') or !extension_loaded('pdo_'.$driver)) {
      throw new \RuntimeException(
        'Please navigate to "http://php.net/manual/pdo.installation.php" '.
        ' to find out how to install "PDO" with "pdo_'.$driver.'" on your system!'
      );
    }

    $driver = '\Pimf\Pdo\\'.ucfirst($driver);

    $pdo = new $driver();

    return $pdo->connect($config);
  }
}
