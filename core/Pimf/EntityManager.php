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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf;
use Pimf\DataMapper\Base, Pimf\Database;

/**
 * Based on PDO it is a general manager for data persistence and object relational mapping.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 */
class EntityManager extends Base
{
  /**
   * @var string The namespace name of data-mappers repository.
   */
  protected $prefix;

  /**
   * @param \PDO $db
   * @param string $prefix The data-mappers repository name.
   */
  public function __construct(\PDO $db, $prefix = '\Pimf')
  {
    parent::__construct($db);
    $this->prefix = $prefix . '\DataMapper\\';
  }

  /**
   * @param string $entity The name of the data-mapper class.
   * @return Base
   * @throws \BadMethodCallException If no entity fount at the repository.
   */
  public function load($entity)
  {
    $entity = $this->prefix . ucfirst($entity);

    if (true === $this->identityMap->hasId($entity)) {
      return $this->identityMap->getObject($entity);
    }

    if (!class_exists($entity)) {
      throw new \BadMethodCallException(
        'entity "'.$entity.'" found at the data-mapper repository'
      );
    }

    $model = new $entity($this->db);

    $this->identityMap->set($entity, $model);

    return $this->identityMap->getObject($entity);
  }

  /**
   * @return bool
   */
  public function beginTransaction()
  {
    return $this->db->beginTransaction();
  }

  /**
   * @return bool
   */
  public function commitTransaction()
  {
    return $this->db->commit();
  }

  /**
   * @return bool
   */
  public function rollbackTransaction()
  {
    return $this->db->rollBack();
  }

  /**
   * @param string $entity
   * @return Base
   */
  public function __get($entity)
  {
    return $this->load($entity);
  }

  /**
   * @return Database
   */
  public function getPDO()
  {
    return $this->db;
  }
}
