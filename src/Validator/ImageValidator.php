<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Validator;

/**
 * Provides file validation functionality.
 */
class ImageValidator extends AbstractValidator {

	/**
	 * Validate the image height matches the size.
	 *
	 * @access public
	 * @param int $size
	 * @return boolean
	 */
	public function height($size) {
		return ($this->getFile()->height() == $size);
	}

	/**
	 * Validate the image width matches the size.
	 *
	 * @access public
	 * @param int $size
	 * @return boolean
	 */
	public function width($size) {
		return ($this->getFile()->width() == $size);
	}

	/**
	 * Validate image height is less than or equal to the max.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function maxHeight($max) {
		return ($this->getFile()->height() <= $max);
	}

	/**
	 * Validate image width is less than or equal to the max.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function maxWidth($max) {
		return ($this->getFile()->width() <= $max);
	}

	/**
	 * Validate image height is greater than or equal to the minimum.
	 *
	 * @access public
	 * @param int $min
	 * @return boolean
	 */
	public function minHeight($min) {
		return ($this->getFile()->height() >= $min);
	}

	/**
	 * Validate image width is greater than or equal to the minimum.
	 *
	 * @access public
	 * @param int $min
	 * @return boolean
	 */
	public function minWidth($min) {
		return ($this->getFile()->width() >= $min);
	}

}