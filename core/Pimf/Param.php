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
class Pimf_Param
{
  /**
   * @var ArrayObject
   */
  protected $data = null;

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    $this->data = new ArrayObject($data, ArrayObject::STD_PROP_LIST);
  }

  /**
   * @return array
   */
  public function getAll()
  {
    return $this->data->getArrayCopy();
  }

  /**
   * @param string $index
   * @param null $defaultValue
   * @return mixed|null
   */
  public function get($index, $defaultValue = null)
  {
    if($this->data->offsetExists($index)) {
      return $this->data->offsetGet($index);
    }

    return $defaultValue;
  }

  /**
   * @param string $index
   * @param null $defaultValue
   * @return mixed|null
   */
  public function getParam($index, $defaultValue = null)
  {
    return $this->get($index, $defaultValue);
  }
}
