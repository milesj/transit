<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer;

use Transit\File;

/**
 * Provides shared functionality for transformers.
 */
abstract class AbstractTransformer implements Transformer {

	/**
	 * File object.
	 *
	 * @access protected
	 * @var \Transit\File
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
	 * @return \Transit\File
	 */
	public function getFile() {
		return $this->_file;
	}

	/**
	 * Set the File object.
	 *
	 * @access public
	 * @param \Transit\File $file
	 * @return \Transit\Transformer\Transformer
	 */
	public function setFile(File $file) {
		$this->_file = $file;

		return $this;
	}

}