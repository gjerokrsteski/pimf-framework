<?php
/**
 * Pimf_Models_Json
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
 * Defines the general behavior of sending JSON data.
 *
 * You have to extend it.
 *
 * @package Pimf_Model_ToArray
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Pimf_Model_ToArray
{
  /**
   * Returns the properties of the given model-object.
   * For another properties output format, please override this method.
   * @return array A list of properties.
   */
  public function toArray()
  {
    return $this->mapProperties(
      $this->fetchClassVars()
    );
  }

  /**
   * Get the default properties of the class.
   * @return array
   */
  protected function fetchClassVars()
  {
    return get_class_vars(get_class($this));
  }

  /**
   * Maps the properties to array with actual values.
   * For another properties-mapping, please override this method.
   * @param array $properties
   * @return array
   */
  protected function mapProperties(array $properties)
  {
    $propertiesMap = array();

    foreach ($properties as $name => $defaultVal) {
      $propertiesMap[$name] = (true === empty($this->$name)) ? $defaultVal : $this->$name;
    }

    return $propertiesMap;
  }
}