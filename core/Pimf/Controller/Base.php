<?php
/**
 * Controller
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

namespace Pimf\Controller;
use \Pimf\Param,  \Pimf\Registry, \Pimf\Environment, \Pimf\Controller\Exception as Bomb, \Pimf\Request;

/**
 * Defines the general controller behaviour - you have to extend it.
 *
 * @package Controller
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Base
{
  /**
   * @var Request
   */
  protected $request;

  /**
   * @param \Pimf\Request $request
   */
  public function __construct(\Pimf\Request $request)
  {
    $this->request = $request;
  }

  abstract public function indexAction();

  /**
   * Method to show the content.
   * @return mixed
   * @throws \Pimf\Controller\Exception if no action found
   * @throws \RuntimeException if bad request method
   */
  public function render()
  {
    $conf = Registry::get('conf');

    if (Environment::isCli() && $conf['environment'] == 'production') {

      $suffix = 'CliAction';
      $action = $this->request->fromCli()->get('action') ?: 'index';

    } else {

      $requestMethod = ucfirst(Registry::get('env')->getRequestMethod());
      $suffix        = 'Action';

      if (!method_exists($this->request, $bag = 'from'.$requestMethod)) {
        throw new Bomb("not supported request method=".$requestMethod);
      }

      $action = $this->request->{$bag}()->get('action') ?: 'index';

      if($conf['app']['routeable'] === true) {

        $target = Registry::get('router')->find();

        if($target instanceof \Pimf\Route\Target) {

          $action = $target->getAction();

          Request::$getData = new Param(Request::stripSlashesIfMagicQuotes(
            array_merge($target->getParams(), Request::$getData->getAll())
          ));
        }
      }
    }

    $action = strtolower($action) . $suffix;

    if (method_exists($this, 'init')) {
      call_user_func(array($this, 'init'));
    }

    if (!method_exists($this, $action)) {
      throw new Bomb(
        "no action '{$action}' defined at controller ". get_class($this)
      );
    }

    return call_user_func(array($this, $action));
  }

  /**
   * Prepares the response object to return an HTTP Redirect response to the client.
   *
   * @param string $route The redirect destination like controller/action
   */
  public function redirect($route)
  {
    $url = \Pimf\Url::compute($route);
    \Pimf\Util\Header::sendFound();
    \Pimf\Util\Header::toLocation($url);
  }
}