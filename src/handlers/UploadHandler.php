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

	/**
	 * Upload the file to the target directory.
	 *
	 * @access public
	 * @param boolean $overwrite
	 * @return \mjohnson\transit\File
	 * @throws \Exception
	 */
	public function upload($overwrite = false) {
		$data = $this->_data;

		// Validate errors
		if ($data['error'] > 0 || !is_uploaded_file($data['tmp_name']) || !is_file($data['tmp_name'])) {
			switch ($data['error']) {
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$error = 'File exceeds the maximum file size.';
				break;
				case UPLOAD_ERR_PARTIAL:
					$error = 'File was only partially uploaded.';
				break;
				case UPLOAD_ERR_NO_FILE:
					$error = 'No file was found for upload.';
				break;
				default:
					$error = 'File failed to upload.';
				break;
			}

			throw new Exception($error);
		}

		// Upload the file
		$target = $this->findTarget($data['name'], $overwrite);

		if (move_uploaded_file($data['tmp_name'], $target) || copy($data['tmp_name'], $target)) {
			return new File($target);
		}

		throw new Exception('An unknown error has occurred.');
	}

}