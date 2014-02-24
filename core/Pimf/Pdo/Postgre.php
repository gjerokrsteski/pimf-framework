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
 * Connection management to PostgreSQL
 *
 * @package Database
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Postgre extends Connector
{
  protected $options = array(
    \PDO::ATTR_CASE              => \PDO::CASE_LOWER,
    \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_ORACLE_NULLS      => \PDO::NULL_NATURAL,
    \PDO::ATTR_STRINGIFY_FETCHES => false,
  );

  /**
   * @param array $config
   * @return \Pimf\Database
   */
  public function connect(array $config)
  {
    $dsn = "pgsql:host={$config['host']};dbname={$config['database']}";

    if (isset($config['port'])) {
      $dsn .= ";port={$config['port']}";
    }

    $connection = new \Pimf\Database($dsn, $config['username'], $config['password'], $this->options($config));

    // set to UTF-8 which should be fine for most scenarios.
    if (isset($config['charset'])) {
      $connection->prepare("SET NAMES '{$config['charset']}'")->execute();
    }

    // If a schema has been specified
    if (isset($config['schema'])) {
      $connection->prepare("SET search_path TO '{$config['schema']}'")->execute();
    }

    return $connection;
  }
}