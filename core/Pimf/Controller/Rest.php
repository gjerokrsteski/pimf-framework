<?php
/**
 * Controller
 *
 * @copyright Copyright (c)  Gjero Krsteski (http://krsteski.de)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\Controller;

/**
 * Base class for representational state transfer app building.
 *
 * REST in PHP can be done pretty simple. Use this abstract controller for REST calls.
 * This works with Apache and Lighttpd out of the box, and no rewrite rules are needed.
 *
 * @package Controller
 * @author  Gjero Krsteski <gjero@krsteski.de>
 */
class Rest extends Base
{
    public function init()
    {
        // allow cross-origin resource sharing
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
    }

    /**
     * Can be overridden.
     */
    public function indexAction()
    {
        //...
    }
}
