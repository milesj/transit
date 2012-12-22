<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\handlers;

use mjohnson\transit\File;
use \Exception;

/**
 * Handles the upload process for files.
 *
 * @package	mjohnson.transit.handlers
 */
class UploadHandler extends AbstractHandler {

	/**
	 * Form post data.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data;

	/**
	 * Store the $_FILES data.
	 *
	 * @access public
	 * @param array $data
	 * @throws \Exception
	 */
	public function __construct(array $data) {
		if (empty($data['tmp_name'])) {
			throw new Exception(sprintf('Invalid upload; no tmp_name detected!'));
		}

		$this->_data = $data;
	}



}