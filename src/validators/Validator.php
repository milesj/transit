<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\validators;

use mjohnson\transit\File;

/**
 * Interface for all validators to implement.
 *
 * @package	mjohnson.transit.validators
 */
interface Validator {

	/**
	 * Add a validation rule with an error message and custom params.
	 *
	 * @access public
	 * @param string $method
	 * @param string $message
	 * @param mixed $params
	 * @return \mjohnson\transit\validators\Validator
	 */
	public function addRule($method, $message, $params = array());

	/**
	 * Return the File object.
	 *
	 * @access public
	 * @return \mjohnson\transit\File
	 */
	public function getFile();

	/**
	 * Set the File object.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @return \mjohnson\transit\validators\Validator
	 */
	public function setFile(File $file);

	/**
	 * Validate that all the rules pass.
	 *
	 * @access public
	 * @return boolean
	 */
	public function validate();

}