<?php
/**
 * DataMapper
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

namespace Pimf\DataMapper;
use Pimf\Util\IdentityMap;

/**
 * For mapping the domain models to the persistence layer.
 *
 * Defines the general behaviour for the data-mappers - you have to extend it.
 *
 * You have to use it if you want to persist data.
 *
 * @package DataMapper
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 * @method insert($entity)
 * @method update($entity)
 * @method delete($entity)
 * @method find($id)
 */
abstract class Base
{
  /**
   * @var \PDO The database resource.
   */
  protected $db;

  /**
   * @var \Pimf\Util\IdentityMap
   */
  protected $identityMap;

  /**
   * @param \PDO $db
   */
  public function __construct(\PDO $db)
  {
    $this->db          = $db;
    $this->identityMap = new IdentityMap();
  }

  public function __destruct()
  {
    unset($this->identityMap, $this->db);
  }

  /**
   * Makes a given model-property accessible.
   * @param object $model
   * @param int $value
   * @param string $property
   * @return mixed
   */
  public function reflect($model, $value, $property = 'id')
  {
    $attribute = new \ReflectionProperty($model, $property);
    $attribute->setAccessible(true);
    $attribute->setValue($model, $value);

    return $model;
  }
}