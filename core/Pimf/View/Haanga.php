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
use Pimf\Contracts\Reunitable, Pimf\View, Pimf\Registry, Pimf\Util\Value, Pimf\util\String as String;

/**
 * A view for HAANGA template engine that uses Django syntax - fast and secure template engine for PHP.
 *
 * For use please add the following code to the end of the config.app.php file:
 *
 * <code>
 *
 * 'view' => array(
 *
 *   'haanga' => array(
 *     'cache'       => true,  // if compilation caching should be used
 *     'debug'       => false, // if set to true, you can display the generated nodes
 *     'auto_reload' => true,  // useful to recompile the template whenever the source code changes
 *  ),
 *
 * ),
 *
 * </code>
 *
 * @link http://haanga.org/documentation
 * @package View
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Haanga extends View implements Reunitable
{
  /**
   * @param string $template
   * @param array  $data
   */
  public function __construct($template, array $data = array())
  {
    parent::__construct($template, $data);

    $conf = Registry::get('conf');

    $options = array(
      'debug'        => Value::ensureBoolean($conf['view']['haanga']['debug']),
      'template_dir' => $this->path,
      'autoload'     => Value::ensureBoolean($conf['view']['haanga']['auto_reload']),
    );

    if($conf['view']['haanga']['cache'] === true){
      $options['cache_dir'] = $this->path.'/haanga_cache';
    }

    $root = String::ensureTrailing('/', dirname(dirname(dirname(dirname(dirname(__FILE__))))));

    require_once $root."Haanga/lib/Haanga.php";

    \Haanga::configure($options);
  }

  /**
   * Puts the template an the variables together.
   * @return NULL|string|void
   */
  public function reunite()
  {
    return \Haanga::Load(
      $this->template,
      $this->data->getArrayCopy()
    );
  }
}
