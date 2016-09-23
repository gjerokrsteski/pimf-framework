<?php
/**
 * Controller
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Controller;

use \Pimf\Param, \Pimf\Config, \Pimf\Sapi,
    \Pimf\Controller\Exception as Bomb,
    \Pimf\Request, \Pimf\Util\Header, \Pimf\Url,
    \Pimf\Response, \Pimf\EntityManager, \Pimf\Logger,
    \Pimf\Util\Value, \Pimf\Environment, \Pimf\Router;

/**
 * Defines the general controller behaviour - you have to extend it.
 *
 * @package Controller
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
abstract class Base
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Environment
     */
    protected $env;

    /**
     * @param Request       $request
     * @param Response      $response
     * @param Logger        $logger
     * @param EntityManager $em
     * @param Router        $router
     * @param Environment   $env
     */
    public function __construct(
        Request $request,
        Response $response = null,
        Logger $logger,
        $em,
        Router $router,
        Environment $env
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->logger = $logger;
        $this->em = $em;
        $this->router = $router;
        $this->env = $env;
    }

    abstract public function indexAction();

    /**
     * Method to show the content.
     *
     * @return mixed
     * @throws \Exception If not supported request method or bad controller
     */
    public function render()
    {
        if (Sapi::isCli() && Config::get('environment') == 'production') {
            $suffix = 'CliAction';
            $action = $this->request->fromCli()->get('action', 'index');
        } else {

            $suffix = 'Action';

            if ($this->request->getMethod() != 'GET' && $this->request->getMethod() != 'POST') {

                $redirectUrl = new Value($this->env->REDIRECT_URL);
                $redirectUrl = $redirectUrl->deleteLeading('/')->deleteTrailing('/')->explode('/');
                $action = isset($redirectUrl[1]) ? $redirectUrl[1] : 'index';

            } else {
                $bag = sprintf('from%s', ucfirst(strtolower($this->request->getMethod())));
                $action = $this->request->{$bag}()->get('action', 'index');
            }

            if (Config::get('app.routeable') === true && $this->router instanceof \Pimf\Router) {

                $target = $this->router->find();

                if ($target instanceof \Pimf\Route\Target) {

                    $action = $target->getAction();

                    Request::$getData = new Param(
                        array_merge($target->getParams(), Request::$getData->getAll())
                    );
                }
            }
        }

        $action = strtolower($action) . $suffix;

        if (method_exists($this, 'init')) {
            call_user_func(array($this, 'init'));
        }

        if (!method_exists($this, $action)) {
            throw new Bomb("no action '{$action}' defined at controller " . get_class($this));
        }

        return call_user_func(array($this, $action));
    }

    /**
     * Prepares the response object to return an HTTP Redirect response to the client.
     *
     * @param string  $route     The redirect destination like controller/action
     * @param boolean $permanent If permanent redirection or not.
     * @param boolean $exit
     */
    public function redirect($route, $permanent = false, $exit = true)
    {
        $url = Url::compute($route);

        ($permanent === true) ? Header::sendMovedPermanently() : Header::sendFound();

        Header::toLocation($url, $exit);
    }
}
