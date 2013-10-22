<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Component;

use Transit\Component;

/**
 * Provides shared functionality for classes.
 *
 * @package Transit\Component
 */
class AbstractComponent implements Component {

    /**
     * Configuration.
     *
     * @type array
     */
    protected $_config = array();

    /**
     * Store configuration.
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {
        $this->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($key = null) {
        if ($key === null) {
            return $this->_config;
        }

        return isset($this->_config[$key]) ? $this->_config[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($key, $value = null) {
        if (is_array($key)) {
            $this->_config = array_replace($this->_config, $key);
        } else {
            $this->_config[$key] = $value;
        }

        return $this;
    }

}