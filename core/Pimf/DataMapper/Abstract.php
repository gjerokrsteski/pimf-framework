<?php
/**
 * Pimf_DataMapper
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
 * For mapping the domain models to the persistence layer.
 *
 * Defines the general behaviour for the data-mappers - you have to extend it.
 *
 * You have to use it if you want to persist data.
 *
 * @package Pimf_DataMapper
 * @author Gjero Krsteski <gjero@krsteski.de>
 *
 * @method insert($entity)
 * @method update($entity)
 * @method delete($entity)
 * @method find($id)
 */
abstract class Pimf_DataMapper_Abstract
{
  /**
   * @var PDO The database resource.
   */
  protected $db;

  /**
   * @var Pimf_Util_IdentityMap
   */
  protected $identityMap;

  /**
   * @param PDO $db
   */
  public function __construct(PDO $db)
  {
    $this->db          = $db;
    $this->identityMap = new Pimf_Util_IdentityMap();
  }

  public function __destruct()
  {
    unset($this->identityMap, $this->db);
  }

  /**
   * @param object $model
   * @param int $idValue
   * @param string $idKey
   * @return mixed
   */
  public function reflectId($model, $idValue, $idKey = 'id')
  {
    $attribute = new ReflectionProperty($model, $idKey);
    $attribute->setAccessible(true);
    $attribute->setValue($model, $idValue);

    return $model;
  }
}