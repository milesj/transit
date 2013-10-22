<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

/**
 * Provides shared functionality for component classes.
 *
 * @package Transit
 */
interface Component {

    /**
     * Get all config or a single config.
     *
     * @param string $key
     * @return mixed
     */
    public function getConfig($key = null);

    /**
     * Set configuration.
     *
     * @param string|array $key
     * @param mixed $value
     * @return \Transit\Component
     */
    public function setConfig($key, $value);

}