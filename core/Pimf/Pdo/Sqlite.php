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
 * Connection management to SQLite.
 *
 * @package Pimf_Pdo
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Pdo_Sqlite extends Pimf_Pdo_Connector
{
  /**
   * @param array $config
   * @return PDO
   */
  public function connect(array $config)
  {
    $options = $this->options($config);

    // SQLite provides supported for "in-memory" databases, which exist only for
    // lifetime of the request. These are mainly for tests.
    if ($config['database'] == ':memory:') {
      return new Pimf_Pdo('sqlite::memory:', null, null, $options);
    }

    return new Pimf_Pdo('sqlite:' . $config['database'], null, null, $options);
  }
}
