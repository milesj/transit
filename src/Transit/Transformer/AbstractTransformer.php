<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer;

use Transit\Transformer;

/**
 * Provides shared functionality for transformers.
 *
 * @package Transit\Transformer
 */
abstract class AbstractTransformer implements Transformer {

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
		$this->_config = $config + $this->_config;
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
	public function setConfig($key, $value) {
		$this->_config[$key] = $value;

		return $this;
	}

}