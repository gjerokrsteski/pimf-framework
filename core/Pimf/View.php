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
 * A simply view for sending and rendering data.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_View
{
  /**
   * @var string Name of the template.
   */
  protected $template;

  /**
   * Contains the variables that are to be embedded in the template.
   * @var array
   */
  protected $data;

  /**
   * @param string $template
   */
  public function __construct($template = 'default.phtml')
  {
    $this->data     = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    $conf           = Pimf_Registry::get('conf');
    $this->path     = dirname(dirname(dirname(__FILE__))) . '/app/' . $conf['app']['name'] . '/_templates';
    $this->template = (string)$template;
  }

  /**
   * @param string $template
   * @return Pimf_View
   */
  public function produce($template)
  {
    $view = clone $this;
    $view->template = (string)$template;
    return $view;
  }

  /**
   * @param string $template
   * @param array $model
   * @return mixed
   */
  public function partial($template, array $model = array())
  {
    return $this->produce($template)->pump($model)->render();
  }

  /**
   * @param string $template
   * @param array $model
   * @return string
   */
  public function loop($template, array $model = array())
  {
    $out = '';

    foreach ($model as $row) {
      $out .= $this->partial($template, $row);
    }

    return $out;
  }

  /**
   * Assigns a variable to a specific key for the template.
   * @param string $key The key.
   * @param mixed $value The Value.
   * @return Pimf_View
   */
  public function assign($key, $value)
  {
    $this->data[$key] = $value;
    return $this;
  }

  /**
   * Exchange all variables.
   * @param array $model
   * @return Pimf_View
   */
  public function pump(array $model)
  {
    $this->data->exchangeArray($model);
    return $this;
  }

  /**
   * Is utilized for reading data from inaccessible properties.
   * @param string $name
   * @return mixed|null
   */
  public function __get($name)
  {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }

    $trace = debug_backtrace();
    trigger_error(
      'undefined property for the view: '
        . $name . ' at ' . $trace[0]['file']
        . ' line ' . $trace[0]['line'], E_USER_NOTICE
    );

    return null;
  }

  /**
   * @return string The Output of the template.
   * @throws RuntimeException If could not find template.
   * @throws Exception If previous thrown.
   */
  public function render()
  {
    $level = ob_get_level();
    ob_start();

    try {

      echo $this->reunite();

    } catch (Exception $e) {

      while (ob_get_level() > $level) {
        ob_end_clean();
      }

      throw $e;
    }

    return ob_get_clean();
  }

  /**
   * Puts the template an the variables together.
   * @throws RuntimeException
   * @return string
   */
  public function reunite()
  {
    $file = $this->path . '/' . $this->template;

    if (!file_exists($file)) {
      throw new RuntimeException('could not find template: ' . $file);
    }

    if (!is_readable($file)) {
      throw new RuntimeException('could not read template: ' . $file);
    }

    include $file;
  }
}
