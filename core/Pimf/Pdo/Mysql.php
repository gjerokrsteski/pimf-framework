<?php
/**
 * Pimf_Pdo
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
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * Connection management to MySQL.
 *
 * @package Pimf_Pdo
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Pdo_Mysql extends Pimf_Pdo_Connector
{
  /**
   * @param array $config
   * @return Pimf_Pdo
   */
  public function connect(array $config)
  {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']}";

    if (isset($config['port'])) {
      $dsn .= ";port={$config['port']}";
    }

    if (isset($config['unix_socket'])) {
      $dsn .= ";unix_socket={$config['unix_socket']}";
    }

    $connection = new Pimf_Pdo($dsn, $config['username'], $config['password'], $this->options($config));

    // set to UTF-8 which should be fine for most scenarios.
    if (isset($config['charset'])) {
      $connection->prepare("SET NAMES '{$config['charset']}'")->execute();
    }

    return $connection;
  }
}