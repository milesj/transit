<?php

namespace mjohnson\transit;

use \Exception;

class File {

	/**
	 * Absolute file path.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Store the file path.
	 *
	 * @access public
	 * @param string $path
	 * @throws \Exception
	 */
	public function __construct($path) {
		if (!file_exists($path)) {
			throw new Exception(sprintf('%s does not exist.', $path));
		}

		$this->_path = $path;
	}

	/**
	 * Delete the file.
	 *
	 * @access public
	 * @return boolean
	 */
	public function delete() {
		clearstatcache();

		return @unlink($this->_path);
	}

	/**
	 * Return the dimensions of the file if it is an image.
	 *
	 * @access public
	 * @return array
	 */
	public function dimensions() {
		$data = @getimagesize($this->_path);

		if ($data && is_array($data)) {
			return array(
				'width' => $data[0],
				'height' => $data[1],
				'type' => $data['mime']
			);
		}

		if (!$data) {
			$image = @imagecreatefromstring(file_get_contents($this->_path));

			return array(
				'width' => @imagesx($image),
				'height' => @imagesy($image),
				'type' => '' //self::mimeType($path)
			);
		}

		return null;
	}

	/**
	 * Return the directory the file is in.
	 *
	 * @access public
	 * @return string
	 */
	public function dir() {
		return dirname($this->_path) . '/';
	}

	/**
	 * Return the extension.
	 *
	 * @access public
	 * @return string
	 */
	public function ext() {
		return mb_strtolower(trim(mb_strrchr($this->_path, '.'), '.'));
	}

	/**
	 * Return the file name.
	 *
	 * @access public
	 * @return string
	 */
	public function name() {
		return basename($this->_path);
	}

	/**
	 * Return the file size.
	 *
	 * @access public
	 * @return int
	 */
	public function size() {
		return filesize($this->_path);
	}

}