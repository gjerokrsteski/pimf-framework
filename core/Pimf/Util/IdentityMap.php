<?php
/**
 * Util
 *
 * PHP Version 5
 *
 * A comprehensive collection of PHP utility classes and functions
 * that developers find themselves using regularly when writing web applications.
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

namespace Pimf\Util;

/**
 * The identity map pattern is a database access design pattern used to improve performance
 * by providing a context-specific, in-memory cache to prevent duplicate retrieval of the
 * same object data from the database.
 *
 * @package Util
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class IdentityMap
{
  /**
   * @var \ArrayObject
   */
  protected $idToObject;

  /**
   * @var \SplObjectStorage
   */
  protected $objectToId;

  public function __construct()
  {
    $this->objectToId = new \SplObjectStorage();
    $this->idToObject = new \ArrayObject();
  }

  /**
   * @param integer $id
   * @param mixed $object
   */
  public function set($id, $object)
  {
    $this->idToObject[$id]     = $object;
    $this->objectToId[$object] = $id;
  }

  /**
   * @param mixed $object
   * @throws \OutOfBoundsException
   * @return integer
   */
  public function getId($object)
  {
    if (false === $this->hasObject($object)) {
      throw new \OutOfBoundsException(
        'no object='.get_class($object).' at the identity-map'
      );
    }

    return $this->objectToId[$object];
  }

  /**
   * @param integer $id
   * @return boolean
   */
  public function hasId($id)
  {
    return isset($this->idToObject[$id]);
  }

  /**
   * @param mixed $object
   * @return boolean
   */
  public function hasObject($object)
  {
    return isset($this->objectToId[$object]);
  }

  /**
   * @param integer $id
   * @throws \OutOfBoundsException
   * @return object
   */
  public function getObject($id)
  {
    if (false === $this->hasId($id)) {
      throw new \OutOfBoundsException(
        'no id='.$id.' at the identity-map'
      );
    }

    return $this->idToObject[$id];
  }
}