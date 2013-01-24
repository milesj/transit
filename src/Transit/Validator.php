<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\File;

/**
 * Interface for all validators to implement.
 */
interface Validator {

	/**
	 * Add a validation rule with an error message and custom params.
	 *
	 * @param string $method
	 * @param string $message
	 * @param mixed $params
	 * @return \Transit\Validator
	 */
	public function addRule($method, $message, $params = array());

	/**
	 * Return the File object.
	 *
	 * @return \Transit\File
	 */
	public function getFile();

	/**
	 * Set the File object.
	 *
	 * @param \Transit\File $file
	 * @return \Transit\Validator
	 */
	public function setFile(File $file);

	/**
	 * Validate that all the rules pass.
	 *
	 * @return bool
	 */
	public function validate();

}