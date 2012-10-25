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
 * Abstract class for connections and connection management.
 *
 * @package Pimf_Pdo
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Pimf_Pdo_Connector
{
  /**
   * The PDO connection options.
   * @var array
   */
  protected $options = array(
    PDO::ATTR_CASE              => PDO::CASE_LOWER,
    PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
    PDO::ATTR_STRINGIFY_FETCHES => false,
    PDO::ATTR_EMULATE_PREPARES  => false,
  );

  /**
   * Establish a PDO database connection.
   * @param array $config
   * @return Pimf_PDO
   */
  abstract public function connect(array $config);

  /**
   * Get the PDO connection options for the configuration.
   * Developer specified options will override the default connection options.
   * @param array $config
   * @return array
   */
  protected function options($config)
  {
    $options = (isset($config['options'])) ? $config['options'] : array();

    return $this->options + $options;
  }
}