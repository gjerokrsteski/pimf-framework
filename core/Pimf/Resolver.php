<?php
/**
 * Pimf
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Pimf;

use Pimf\Resolver\Exception as Bomb;
use Pimf\Util\Character as Str;

/**
 * Resolves the user requests to controller and action.
 *
 * @package Pimf
 * @author  Gjero Krsteski <gjero@krsteski.de>
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
     * @var Router
     */
    protected $router;

    /**
     * @param Request      $request
     * @param string       $repositoryPath
     * @param string       $prefix
     * @param \Pimf\Router $router
     *
     * @throws \Pimf\Resolver\Exception If no controller found at the repository path
     */
    public function __construct(\Pimf\Request $request, $repositoryPath = '/Controller', $prefix = 'Pimf\\', $router)
    {
        $controllerName = $request->fromGet()->get('controller');
        $this->router = $router;

        if (Config::get('app.routeable') === true) {

            $target = $this->router->find();

            if ($target instanceof \Pimf\Route\Target) {
                $controllerName = $target->getController();
            }
        }

        if (Sapi::isCli() && Config::get('environment') == 'production') {
            $controllerName = $request->fromCli()->get('controller');
        }

        if (!$controllerName) {
            $controllerName = Config::get('app.default_controller');
        }

        $this->repositoryPath = $repositoryPath;
        $this->request = $request;
        $this->controllerClass = $prefix . 'Controller\\';

        $basepath = $this->repositoryPath . '/';
        $controller = ucfirst($controllerName);

        if (Str::isEvilPath($basepath . $controller)) {
            throw new Bomb('directory traversal attack is not funny!');
        }

        $this->controllerPath = $basepath . $controller . '.php';

        if (!file_exists($this->controllerPath)) {
            throw new Bomb('no "' . $controller . '" controller found at the repository path');
        }
    }

    /**
     * @param Environment   $env
     * @param Logger        $logger
     * @param EntityManager $em
     *
     * @return \Pimf\Controller\Base
     * @throws \Pimf\Resolver\Exception If no controller specified or no controller found at the repository.
     */
    public function process(Environment $env, EntityManager $em, Logger $logger)
    {
        $path = str_replace($this->repositoryPath, '', $this->controllerPath);
        $name = str_replace('/', $this->controllerClass, $path);
        $controller = str_replace('.php', '', $name);

        if (!class_exists($controller)) {
            throw new Bomb('can not load class "' . $controller . '" from the repository');
        }

        return new $controller(
            $this->request, new Response($this->request->getMethod()), $logger, $em, $this->router, $env
        );
    }
}
