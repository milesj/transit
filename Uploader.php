<?php

namespace mjohnson\transit;

use \Exception;

class Uploader {

	/**
	 * Form post data.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data;

	/**
	 * Should we scan the file for viruses? Requires ClamAV module: http://clamav.net/
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_scanFile = false;

	/**
	 * Destination upload directory.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_uploadDir = '';

	/**
	 * Store the $_FILES data.
	 *
	 * @access public
	 * @param array $data
	 */
	public function __construct(array $data) {
		if (empty($data['tmp_name'])) {
			throw new Exception(sprintf('Invalid upload; no tmp_name detected!'));
		}

		$this->_data = $data;
	}

	/**
	 * Set the upload directory and create it if it doesn't exist.
	 *
	 * @access public
	 * @param string $path
	 * @return \mjohnson\transit\Uploader
	 */
	public function setUploadDirectory($path) {
		if (substr($path, -1) != '/') {
			$path .= '/';
		}

		if (!file_exists($path)) {
			mkdir($path, 0777, true);

		} else if (!is_writable($path)) {
			chmod($path, 0777);
		}

		$this->_uploadDir = $path;

		return $this;
	}

	/**
	 * Enable or disable virus scanning.
	 *
	 * @access public
	 * @param boolean $status
	 * @return \mjohnson\transit\Uploader
	 */
	public function setVirusScan($status) {
		$this->_scanFile = (bool) $status;

		return $this;
	}

	/**
	 * Upload the file to the target directory.
	 *
	 * @access public
	 * @return \mjohnson\transit\File
	 * @throws \Exception
	 */
	public function upload() {
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

		// Scan the file using ClamAV
		if ($this->_scanFile && extension_loaded('clamav')) {
			cl_setlimits(5, 1000, 200, 0, 10485760);

			if (cl_scanfile($data['tmp_name'])) {
				throw new Exception('Virus detected in file upload.');
			}
		}

		// Upload the file
		$target = $this->_uploadDir . $data['name'];

		if (move_uploaded_file($data['tmp_name'], $target) || copy($data['tmp_name'], $target)) {
			return new File($target);
		}

		throw new Exception('An unknown error has occurred.');
	}

}