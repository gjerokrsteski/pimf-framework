<?php
/**
 * Pimf
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
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Pdo extends PDO
{
  /**
   * The current transaction level.
   * @var int
   */
  protected $transLevel = 0;

  /**
   * Check database drivers that support savepoints.
   * @return bool
   */
  protected function nestable()
  {
    return in_array(
      $this->getAttribute(PDO::ATTR_DRIVER_NAME),
      array("pgsql", "mysql")
    );
  }

  /**
   * @return bool|void
   */
  public function beginTransaction()
  {
    if ($this->transLevel == 0 || !$this->nestable()) {
      parent::beginTransaction();
    } else {
      $this->exec("SAVEPOINT LEVEL{$this->transLevel}");
    }

    $this->transLevel++;
  }

  /**
   * @return bool|void
   */
  public function commit()
  {
    $this->transLevel--;

    if ($this->transLevel == 0 || !$this->nestable()) {
      parent::commit();
    } else {
      $this->exec("RELEASE SAVEPOINT LEVEL{$this->transLevel}");
    }
  }

  /**
   * @return bool|void
   * @throws PDOException
   */
  public function rollBack()
  {
    if ($this->transLevel == 0) {
      throw new PDOException(
        'trying to rollback without a transaction-start'
      );
    }

    $this->transLevel--;

    if ($this->transLevel == 0 || !$this->nestable()) {
      parent::rollBack();
    } else {
      $this->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transLevel}");
    }
  }
}