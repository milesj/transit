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
				'height' => $data[1]
			);
		}

		if (!$data) {
			$image = @imagecreatefromstring(file_get_contents($this->_path));

			return array(
				'width' => @imagesx($image),
				'height' => @imagesy($image)
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
		return mb_strtolower(pathinfo($this->_path, PATHINFO_EXTENSION));
	}

	/**
	 * Move the file to a new directory.
	 * If a file with the same name already exists, either overwrite or increment file name.
	 *
	 * @access public
	 * @param string $path
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public function move($path, $overwrite = false) {
		$path = str_replace('\\', '/', $path);

		if (substr($path, -1) !== '/') {
			$path .= '/';
		}

		// Determine name and overwrite
		$name = $this->name();
		$ext = $this->ext();

		if (!$overwrite) {
			$no = 1;

			while (file_exists($path . $name . '.' . $ext)) {
				$name = $this->name() . '-' . $no;
				$no++;
			}
		}

		// Move the file
		$targetPath = $path . $name . '.' . $ext;

		if (rename($this->path(), $targetPath)) {
			$this->_path = $targetPath;

			return true;
		}

		return false;
	}

	/**
	 * Return the file name without extension.
	 *
	 * @access public
	 * @param boolean $ext
	 * @return string
	 */
	public function name($ext = false) {
		return pathinfo($this->_path, $ext ? PATHINFO_BASENAME : PATHINFO_FILENAME);
	}

	/**
	 * Return the absolute path.
	 *
	 * @access public
	 * @return string
	 */
	public function path() {
		return $this->_path;
	}

	/**
	 * Rename the file within the current directory.
	 *
	 * @access public
	 * @param string $name
	 * @param string $append
	 * @param string $prepend
	 * @return boolean
	 */
	public function rename($name, $append = '', $prepend = '') {
		if (is_callable($name)) {
			$name = call_user_func_array($name, array($this->name(), $this));
		} else {
			$name = $name ?: $this->name();
		}

		// Add boundaries
		$name = (string) $prepend . $name . (string) $append;

		// Remove unwanted characters
		$name = preg_replace('/[^_-\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/imu', '-', $name);

		// Rename file
		$targetPath = $this->dir() . $name . '.' . $this->ext();

		if (rename($this->path(), $targetPath)) {
			$this->_path = $targetPath;

			return true;
		}

		return false;
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

	/**
	 * Return the mime type.
	 *
	 * @access public
	 * @return string
	 */
	public function type() {
		$f = finfo_open(FILEINFO_MIME);

		list($type, $charset) = explode(';', finfo_file($f, $this->_path));

		finfo_close($f);

		return $type;
	}

}