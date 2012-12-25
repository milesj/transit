<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit;

use mjohnson\transit\exceptions\IoException;
use mjohnson\transit\exceptions\TransformationException;
use mjohnson\transit\exceptions\TransportationException;
use mjohnson\transit\exceptions\ValidationException;
use mjohnson\transit\transformers\Transformer;
use mjohnson\transit\transporters\Transporter;
use mjohnson\transit\validators\Validator;
use \Exception;
use \RuntimeException;
use \InvalidArgumentException;

/**
 * Primary class that handles all aspects of the uploading and importing process.
 * Furthermore provides support for file transformation and transportation.
 *
 * @package	mjohnson.transit
 */
class Transit {

	/**
	 * Form files data or import URI.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data;

	/**
	 * Temp upload directory.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_directory = __DIR__;

	/**
	 * File instance after successful upload or import.
	 *
	 * @access protected
	 * @var \mjohnson\transit\File
	 */
	protected $_file;

	/**
	 * List of Files from transformation.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_files = array();

	/**
	 * List of Transformers to create files from the original file.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_transformers = array();

	/**
	 * List of Transformers to apply to the original file.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_selfTransformers = array();

	/**
	 * Transporter instance.
	 *
	 * @access protected
	 * @var \mjohnson\transit\transporters\Transporter
	 */
	protected $_transporter;

	/**
	 * Validator instance.
	 *
	 * @access protected
	 * @var \mjohnson\transit\validators\Validator
	 */
	protected $_validator;

	/**
	 * Store $_FILES data or URI.
	 *
	 * @access public
	 * @param array|string $data
	 */
	public function __construct($data) {
		$this->_data = $data;
	}

	/**
	 * Add a Transformer to generate new images with.
	 *
	 * @access public
	 * @param \mjohnson\transit\transformers\Transformer $transformer
	 * @return \mjohnson\transit\Transit
	 */
	public function addTransformer(Transformer $transformer) {
		$this->_transformers[] = $transformer;

		return $this;
	}

	/**
	 * Add a Transformer to apply to the original file.
	 *
	 * @access public
	 * @param \mjohnson\transit\transformers\Transformer $transformer
	 * @return \mjohnson\transit\Transit
	 */
	public function addSelfTransformer(Transformer $transformer) {
		$this->_selfTransformers[] = $transformer;

		return $this;
	}

	/**
	 * Find a valid target path taking into account file existence and overwriting.
	 *
	 * @access public
	 * @param \mjohnson\transit\File|string $file
	 * @param boolean $overwrite
	 * @return string
	 */
	public function findDestination($file, $overwrite = false) {
		if ($file instanceof File) {
			$name = $file->name();
			$ext = '.' . $file->ext();

		} else {
			$name = $file;
			$ext = '';

			if ($pos = mb_strrpos($name, '.')) {
				$ext = mb_substr($name, $pos, (mb_strlen($name) - $pos));
				$name = mb_substr($name, 0, $pos);
			}
		}

		$target = $this->_directory . $name . $ext;

		if (!$overwrite) {
			$no = 1;

			while (file_exists($target)) {
				$target = sprintf('%s%s-%s%s', $this->_directory, $name, $no, $ext);
				$no++;
			}
		}

		return $target;
	}

	/**
	 * Return the original file and all transformed files.
	 *
	 * @access public
	 * @return array
	 */
	public function getAllFiles() {
		return array_merge(array($this->getOriginalFile()), $this->getTransformedFiles());
	}

	/**
	 * Return the File that was uploaded or imported.
	 *
	 * @access public
	 * @return \mjohnson\transit\File
	 */
	public function getOriginalFile() {
		return $this->_file;
	}

	/**
	 * Return a list of all transformed Files.
	 *
	 * @access public
	 * @return array
	 */
	public function getTransformedFiles() {
		return $this->_files;
	}

	/**
	 * Copy a local file to the temp directory and return a File object.
	 *
	 * @access public
	 * @param boolean $overwrite
	 * @param boolean $delete
	 * @return boolean
	 * @throws \mjohnson\transit\exceptions\IoException
	 */
	public function importFromLocal($overwrite = true, $delete = false) {
		$path = $this->_data;
		$file = new File($path);
		$target = $this->findDestination($file, $overwrite);

		if (copy($path, $target)) {
			if ($delete) {
				$file->delete();
			}

			$this->_file = new File($target);

			return true;
		}

		throw new IoException(sprintf('Failed to copy %s to new location', $file->basename()));
	}

	/**
	 * Copy a remote file to the temp directory and return a File object.
	 *
	 * @access public
	 * @param boolean $overwrite
	 * @return boolean
	 * @throws \mjohnson\transit\exceptions\IoException
	 * @throws \RuntimeException
	 */
	public function importFromRemote($overwrite = true) {
		if (!function_exists('curl_init')) {
			throw new RuntimeException('The cURL module is required for remote file importing');
		}

		$url = $this->_data;
		$name = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME);

		// Fetch the remote file
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($curl);
		curl_close($curl);

		// Save the file locally
		$target = $this->findDestination($name, $overwrite);

		if (file_put_contents($target, $response)) {
			$this->_file = new File($target);

			return true;
		}

		throw new IoException(sprintf('Failed to import %s from remote location', basename($target)));
	}

	/**
	 * Copy a file from the input stream into the temp directory and return a File object.
	 * Primarily used for Javascript AJAX file uploads.
	 *
	 * @access public
	 * @param boolean $overwrite
	 * @return boolean
	 * @throws \mjohnson\transit\exceptions\IoException
	 */
	public function importFromStream($overwrite = true) {
		$field = $this->_data;

		if (empty($_GET[$field])) {
			throw new IoException(sprintf('%s was not found in the input stream', $field));
		}

		$target = $this->findDestination($_GET[$field], $overwrite);
		$input = fopen('php://input', 'r');
		$output = fopen($target, 'w');

		stream_copy_to_stream($input, $output);

		fclose($input);
		fclose($output);

		$this->_file = new File($target);

		return true;
	}

	/**
	 * Set the temporary directory and create it if it does not exist.
	 *
	 * @access public
	 * @param string $path
	 * @return \mjohnson\transit\Transit
	 */
	public function setDirectory($path) {
		if (substr($path, -1) !== '/') {
			$path .= '/';
		}

		if (!file_exists($path)) {
			mkdir($path, 0777, true);

		} else if (!is_writable($path)) {
			chmod($path, 0777);
		}

		$this->_directory = $path;

		return $this;
	}

	/**
	 * Set the Transporter.
	 *
	 * @access public
	 * @param \mjohnson\transit\transporters\Transporter $transporter
	 * @return \mjohnson\transit\Transit
	 */
	public function setTransporter(Transporter $transporter) {
		$this->_transporter = $transporter;

		return $this;
	}

	/**
	 * Set the Validator.
	 *
	 * @access public
	 * @param \mjohnson\transit\validators\Validator $validator
	 * @return \mjohnson\transit\Transit
	 */
	public function setValidator(Validator $validator) {
		$this->_validator = $validator;

		return $this;
	}

	/**
	 * Apply transformations to the original file and generate new transformed files.
	 *
	 * @access public
	 * @return boolean
	 * @throws \mjohnson\transit\exceptions\IoException
	 * @throws \mjohnson\transit\exceptions\TransformationException
	 */
	public function transform() {
		$originalFile = $this->getOriginalFile();
		$transformedFiles = array();
		$error = null;

		if (!$originalFile) {
			throw new IoException('No original file detected');
		}

		// Create transformed files based off original
		if ($this->_transformers) {
			foreach ($this->_transformers as $transformer) {
				try {
					$transformer->setFile($originalFile);
					$transformedFiles[] = $transformer->transform(false);

				} catch (Exception $e) {
					$error = $e->getMessage();
					break;
				}
			}
		}

		// Apply transformations to original
		if ($this->_selfTransformers && !$error) {
			foreach ($this->_selfTransformers as $transformer) {
				try {
					$transformer->setFile($originalFile);
					$originalFile = $transformer->transform(true);

				} catch (Exception $e) {
					$error = $e->getMessage();
					break;
				}
			}
		}

		// Throw error and rollback
		if ($error) {
			$originalFile->delete();

			foreach ($transformedFiles as $file) {
				$file->delete();
			}

			$this->_file = null;

			throw new TransformationException($error);
		}

		$this->_file = $originalFile;
		$this->_files = $transformedFiles;

		return true;
	}

	/**
	 * Transport the file using the Transporter object.
	 *
	 * @access public
	 * @return array
	 * @throws \mjohnson\transit\exceptions\IoException
	 * @throws \mjohnson\transit\exceptions\TransportationException
	 * @throws \InvalidArgumentException
	 */
	public function transport() {
		if (!$this->_transporter) {
			throw new InvalidArgumentException('No Transporter has been defined');
		}

		$localFiles = $this->getAllFiles();
		$transportedFiles = array();
		$error = null;

		if (!$localFiles) {
			throw new IoException('No files to transport');
		}

		foreach ($localFiles as $file) {
			try {
				$transportedFiles[] = $this->_transporter->transport($file);

			} catch (Exception $e) {
				$error = $e->getMessage();
				break;
			}
		}

		// Throw error and rollback
		if ($error) {
			foreach ($transportedFiles as $path) {
				$this->_transporter->delete($path);
			}

			throw new TransportationException($error);
		}

		return $transportedFiles;
	}

	/**
	 * Upload the file to the target directory.
	 *
	 * @access public
	 * @param boolean $overwrite
	 * @return boolean
	 * @throws \mjohnson\transit\exceptions\ValidationException
	 */
	public function upload($overwrite = false) {
		$data = $this->_data;

		if (empty($data['tmp_name'])) {
			throw new ValidationException('Invalid file detected for upload');
		}

		// Check upload errors
		if ($data['error'] > 0 || !is_uploaded_file($data['tmp_name']) || !is_file($data['tmp_name'])) {
			switch ($data['error']) {
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$error = 'File exceeds the maximum file size';
				break;
				case UPLOAD_ERR_PARTIAL:
					$error = 'File was only partially uploaded';
				break;
				case UPLOAD_ERR_NO_FILE:
					$error = 'No file was found for upload';
				break;
				default:
					$error = 'File failed to upload';
				break;
			}

			throw new ValidationException($error);
		}

		// Validate rules
		if ($this->_validator) {
			$this->_validator
				->setFile(new File($data['tmp_name']))
				->validate();
		}

		// Upload the file
		$target = $this->findDestination($data['name'], $overwrite);

		if (move_uploaded_file($data['tmp_name'], $target) || copy($data['tmp_name'], $target)) {
			$this->_file = new File($target);

			return true;
		}

		throw new ValidationException('An unknown error has occurred');
	}

}