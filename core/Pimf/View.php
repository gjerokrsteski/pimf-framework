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
   * Path to the templates.
   * @var string
   */
  private $path = '_templates';

  /**
   * @var string Name of the template, in which case the default template.
   */
  private $template = 'default';

  /**
   * Contains the variables that are to be embedded in the template.
   * @var array
   */
  private $data;

  public function __construct()
  {
    $registry   = new Pimf_Registry();
    $this->data = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

    $this->setPath(
      dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $registry->conf->app->name
    );
  }

  /**
   * Assigns a variable to a specific key for the template.
   * @param string $key The key.
   * @param mixed $value The Value.
   */
  public function assign($key, $value)
  {
    $this->data[$key] = $value;
  }

  /**
   * @param string $templateName Name of the template.
   */
  public function setTemplate($templateName = 'default')
  {
    $this->template = (string)$templateName;
  }

  /**
   * @param string $templatesDir
   */
  protected function setPath($templatesDir)
  {
    $this->path = (string)$templatesDir . DIRECTORY_SEPARATOR . $this->path;
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
      'undefined property for the view: ' . $name . ' at ' . $trace[0]['file'] . ' line ' . $trace[0]['line'], E_USER_NOTICE
    );

    return null;
  }

  /**
   * @return string The Output of the template.
   * @throws RuntimeException If could not find template.
   */
  public function loadTemplate()
  {
    $file = $this->path . DIRECTORY_SEPARATOR . $this->template . '.phtml';

    if (file_exists($file)) {

      ob_start();
      include $file;
      $output = ob_get_contents();
      ob_end_clean();

      return $output;
    }

    throw new RuntimeException('could not find template: ' . $file);
  }
}
