<?php
/**
 * View
 *
 * @copyright Copyright (c)  Garrett Whitehorn (http://garrettw.net/)
 * @license   http://opensource.org/licenses/MIT MIT License
 */

namespace Pimf\View;

use Pimf\Contracts\Reunitable;
use Pimf\View;
use Pimf\Config;
use Pimf\Cache\Storages\File as FileCache;

/**
 * A view for the Transphporm template engine which uses CSS-style syntax and
 * separates templates from data logic.
 *
 * For use please add the following code to the end of the config.app.php file:
 *
 * <code>
 *
 * 'view' => array(
 *
 *   'transphporm' => array(
 *     'cache'       => true,  // if compilation caching should be used
 *   ),
 *
 * ),
 *
 * </code>
 *
 * @link    https://github.com/Level-2/Transphporm
 * @package View
 * @author  Garrett Whitehorn <gw@garrettw.net>
 * @codeCoverageIgnore
 */
class Transphporm extends View implements Reunitable
{
    /**
     * @var \Pimf\Cache\Storages\File
     */
    protected $cache;

    /**
     * @param string $template
     * @param array $data
     */
    public function __construct($template, array $data = [])
    {
        parent::__construct($template, $data);

        $conf = Config::get('view.transphporm');

        if ($conf['cache'] === true) {
            $this->cache = new FileCache($this->path . '/transphporm_cache/');
        }

        require_once BASE_PATH . "Transphporm/vendor/autoload.php";
    }

    /**
     * Puts the template and the TSS/data together.
     *
     * @return string
     */
    public function reunite()
    {
        $xml = $this->template;
        $xmlpath = $this->path . '/' . $xml;

        $tss = $this->data['tss'];
        $tsspath = $this->path . '/' . $tss;

        $template = new \Transphporm\Builder(
            (is_file($xmlpath)) ? $xmlpath : $xml,
            (is_file($tsspath)) ? $tsspath : $tss
        );

        if (isset($this->cache)) {
            $template->setCache($this->cache);
        }

        if (isset($this->data['data'])) {
            return $template->output($this->data['data'])->body;
        }

        return $template->output()->body;
    }
}
