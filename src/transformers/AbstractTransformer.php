<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers;

use mjohnson\transit\File;

/**
 * Provides shared functionality for transformers.
 *
 * @package	mjohnson.transit.transformers
 */
abstract class AbstractTransformer implements Transformer {

	/**
	 * File object.
	 *
	 * @access protected
	 * @var \mjohnson\transit\File
	 */
	protected $_file;

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Store configuration.
	 *
	 * @access public
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->_config = $config + $this->_config;
	}

	/**
	 * Return the File object.
	 *
	 * @access public
	 * @return \mjohnson\transit\File
	 */
	public function getFile() {
		return $this->_file;
	}

	/**
	 * Set the File object.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @return \mjohnson\transit\transformers\Transformer
	 */
	public function setFile(File $file) {
		$this->_file = $file;

		return $this;
	}

}