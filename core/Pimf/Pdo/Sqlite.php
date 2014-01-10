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
use Pimf\Pdo\Connector;

/**
 * Connection management to SQLite.
 *
 * @package Database
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Sqlite extends Connector
{
  /**
   * @param array $config
   * @return \Pimf\Database
   */
  public function connect(array $config)
  {
    $options = $this->options($config);

    // SQLite provides supported for "in-memory" databases, which exist only for
    // lifetime of the request. These are mainly for tests.
    if ($config['database'] == ':memory:') {
      return new \Pimf\Database('sqlite::memory:', null, null, $options);
    }

    return new \Pimf\Database('sqlite:' . $config['database'], null, null, $options);
  }
}
