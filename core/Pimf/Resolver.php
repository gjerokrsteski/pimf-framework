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
use Pimf\Registry, Pimf\Resolver\Exception as Bomb,
    Pimf\Util\String as Str, Pimf\Response;

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
  protected $controllerPath;

  /**
   * @var string
   */
  protected $controllerClass;

  /**
   * @var string
   */
  protected $repositoryPath;

  /**
   * @var Request
   */
  protected $request;

  /**
   * @param Request $request
   * @param string  $repositoryPath
   * @param string  $prefix
   *
   * @throws Resolver\Exception
   */
  public function __construct(Request $request, $repositoryPath = '/Controller', $prefix = 'Pimf\\')
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

    $this->repositoryPath  = $repositoryPath;
    $this->request         = $request;
    $this->controllerClass = $prefix . 'Controller\\';

    $basepath   = $this->repositoryPath . '/';
    $controller = ucfirst($controllerName);

    if(Str::isEvilPath($basepath.$controller)) {
      throw new Bomb(
        'directory traversal attack is not funny!'
      );
    }

    $this->controllerPath = $basepath . $controller. '.php';

    if (!file_exists($this->controllerPath)) {
      throw new Bomb(
        'no controller found at the repository path; ' . $this->controllerPath
      );
    }
  }

  /**
   * @return \Pimf\Controller\Base
   * @throws \Exception If no controller specified or no controller found at the repository.
   */
  public function process()
  {
    $path       = str_replace($this->repositoryPath, '', $this->controllerPath);
    $name       = str_replace('/', $this->controllerClass, $path);
    $controller = str_replace('.php', '', $name);

    if (!class_exists($controller)) {
      throw new Bomb(
        'can not load class "'.$controller.'" from the repository'
      );
    }

    return new $controller($this->request, new Response(Registry::get('env')->getRequestMethod()));
  }
}
