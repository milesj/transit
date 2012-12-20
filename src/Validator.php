<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit;

/**
 * Provides file validation functionality.
 *
 * @package	mjohnson.transit
 */
class Validator {

	/**
	 * File object.
	 *
	 * @access protected
	 * @var \mjohnson\transit\File
	 */
	protected $_file;

	/**
	 * Create a file object to validate with.
	 *
	 * @access public
	 * @param string $path
	 */
	public function __construct($path) {
		if (is_array($path)) {
			$path = $path['tmp_name'];
		}

		$this->_file = new File($path);
	}

	/**
	 * Validate file size is less than or equal to the max.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function size($max) {
		return ($this->_file->size() <= $max);
	}

	/**
	 * Validate the extension is in the whitelist.
	 *
	 * @access public
	 * @param array $whitelist
	 * @return boolean
	 */
	public function extension($whitelist = array()) {
		return in_array($this->_file->ext(), (array) $whitelist);
	}

	/**
	 * Validate the mime type is in the whitelist.
	 *
	 * @access public
	 * @param array $whitelist
	 * @return boolean
	 */
	public function type($whitelist = array()) {
		return in_array($this->_file->type(), (array) $whitelist);
	}

	/**
	 * Validate the image height matches the size.
	 *
	 * @access public
	 * @param int $size
	 * @return boolean
	 */
	public function height($size) {
		return ($this->_file->height() == $size);
	}

	/**
	 * Validate the image width matches the size.
	 *
	 * @access public
	 * @param int $size
	 * @return boolean
	 */
	public function width($size) {
		return ($this->_file->width() == $size);
	}

	/**
	 * Validate image height is less than or equal to the max.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function maxHeight($max) {
		return ($this->_file->height() <= $max);
	}

	/**
	 * Validate image width is less than or equal to the max.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function maxWidth($max) {
		return ($this->_file->width() <= $max);
	}

	/**
	 * Validate image height is greater than or equal to the minimum.
	 *
	 * @access public
	 * @param int $min
	 * @return boolean
	 */
	public function minHeight($min) {
		return ($this->_file->height() >= $min);
	}

	/**
	 * Validate image width is greater than or equal to the minimum.
	 *
	 * @access public
	 * @param int $min
	 * @return boolean
	 */
	public function minWidth($min) {
		return ($this->_file->width() >= $min);
	}

}