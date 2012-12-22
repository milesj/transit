<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\validators;

use mjohnson\transit\File;
use \Exception;

/**
 * Provides basic file validation functionality.
 *
 * @package	mjohnson.transit.validators
 */
abstract class AbstractValidator implements Validator {

	/**
	 * File object.
	 *
	 * @access protected
	 * @var \mjohnson\transit\File
	 */
	protected $_file;

	/**
	 * Validation rules.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_rules = array();

	/**
	 * Add a validation rule with an error message and custom params.
	 *
	 * @access public
	 * @param string $method
	 * @param string $message
	 * @param mixed $params
	 * @return \mjohnson\transit\validators\Validator
	 */
	public function addRule($method, $message, $params = array()) {
		$this->_rules[$method] = array(
			'message' => (string) $message,
			'params' => (array) $params
		);

		return $this;
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
	 * @return \mjohnson\transit\validators\Validator
	 */
	public function setFile(File $file) {
		$this->_file = $file;

		return $this;
	}

	/**
	 * Validate file size is less than or equal to the max.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function size($max) {
		return ($this->getFile()->size() <= $max);
	}

	/**
	 * Validate the extension is in the whitelist.
	 *
	 * @access public
	 * @param array $whitelist
	 * @return boolean
	 */
	public function extension($whitelist = array()) {
		return in_array($this->getFile()->ext(), (array) $whitelist);
	}

	/**
	 * Validate the mime type is in the whitelist.
	 *
	 * @access public
	 * @param array $whitelist
	 * @return boolean
	 */
	public function type($whitelist = array()) {
		return in_array($this->getFile()->type(), (array) $whitelist);
	}

	/**
	 * Validate that all the rules pass.
	 *
	 * @access public
	 * @return boolean
	 * @throws \Exception
	 */
	public function validate() {
		if (!$this->_rules) {
			return true;
		}

		if (!$this->_file) {
			throw new Exception('No file present for validation');
		}

		foreach ($this->_rules as $method => $rule) {
			if (!method_exists($this, $method)) {
				throw new Exception(sprintf('Validation method %s does not exist', $method));
			}

			if (!call_user_func_array(array($this, $method), $rule['params'])) {
				throw new Exception($rule['message']);
			}
		}

		return true;
	}

}