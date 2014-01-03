<?php
/**
 * View
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

namespace Pimf\View;
use Pimf\Contracts\Reunitable, Pimf\View, Pimf\Registry, Pimf\Util\Value;


/**
 * A view for TWIG a flexible, fast, and secure template engine for PHP.
 *
 * For use please add the following code to the end of the config.app.php file:
 *
 * <code>
 *
 * 'view' => array(
 *
 *   'twig' => array(
 *     'cache'       => true,  // if compilation caching should be used
 *     'debug'       => false, // if set to true, you can display the generated nodes
 *     'auto_reload' => true,  // useful to recompile the template whenever the source code changes
 *  ),
 *
 * ),
 *
 * </code>
 *
 * @link http://twig.sensiolabs.org/documentation
 * @package View
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Twig extends View implements Reunitable
{
  /**
   * @var Twig_Environment
   */
  protected $twig;

  /**
   * @param string $template
   */
  public function __construct($template)
  {
    parent::__construct($template);

    $conf = Registry::get('conf');

    \Twig_Autoloader::register();

    $options = array(
      'debug'       => Value::ensureBoolean($conf['view']['twig']['debug']),
      'auto_reload' => Value::ensureBoolean($conf['view']['twig']['auto_reload']),
    );

    if($conf['view']['twig']['cache'] === true){
      $options['cache'] = $this->path.'/twig_cache';
    }

    // define the Twig environment.
    $this->twig = new \Twig_Environment(
      new \Twig_Loader_Filesystem(array($this->path)),
      $options
    );
  }

  /**
   * @return Twig_Environment
   */
  public function getTwig()
  {
    return $this->twig;
  }

  /**
   * Puts the template an the variables together.
   * @return string|void
   */
  public function reunite()
  {
    return $this->twig->render(
      $this->template,
      $this->data->getArrayCopy()
    );
  }
}
