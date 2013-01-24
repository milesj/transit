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
 */
abstract class AbstractTransformer implements Transformer {

	/**
	 * Configuration.
	 *
	 * @var array
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

}