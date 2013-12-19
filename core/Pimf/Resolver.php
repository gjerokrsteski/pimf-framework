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
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

namespace Pimf;
use Pimf\Registry, Pimf\Resolver\Exception as Bomb;

/**
 * Resolves the user requests to controller and action.
 *
 * @package Pimf
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Resolver
{
  /**
   * @var string
   */
  protected $controllerFilePath;

  /**
   * @var string
   */
  protected $controllerClassName;

  /**
   * @var string
   */
  protected $controllerRepositoryPath;

  /**
   * @var Request
   */
  protected $request;

  /**
   * @param Request $request
   * @param string $controllerRepositoryPath
   * @param string $prefix
   */
  public function __construct(Request $request, $controllerRepositoryPath = '/Controller', $prefix = 'Pimf\\')
  {
    $conf = Registry::get('conf');

    $controllerName = $request->fromGet()->get('controller');

    if($conf['app']['routeable'] === true) {

      $target = Registry::get('router')->find();

      if($target instanceof \Pimf\Route\Target) {
        $controllerName = $target->getController();
      }
    }

    if (Environment::isCli() && $conf['environment'] == 'production') {
      $controllerName = $request->fromCli()->get('controller');
    }

    if (!$controllerName) {
      $controllerName =  $conf['app']['default_controller'];
    }

    $this->controllerRepositoryPath = $controllerRepositoryPath;
    $this->request                  = $request;
    $this->controllerClassName      = $prefix . 'Controller\\';
    $this->controllerFilePath       = $this->controllerRepositoryPath . '/' . ucfirst($controllerName) . '.php';
  }

  /**
   * @return Base
   * @throws Exception If no controller specified or no controller found at the repository.
   */
  public function process()
  {
    if (!file_exists($this->controllerFilePath)) {
      throw new Bomb(
        'no controller found at the repository path; ' . $this->controllerFilePath
      );
    }

    $path       = str_replace($this->controllerRepositoryPath, '', $this->controllerFilePath);
    $name       = str_replace('/', $this->controllerClassName, $path);
    $controller = str_replace('.php', '', $name);

    if (!class_exists($controller)) {
      throw new Bomb(
        'can not load class "'.$controller.'" from the repository'
      );
    }

    return new $controller($this->request);
  }
}
