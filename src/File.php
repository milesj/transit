<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit;

use \Exception;

/**
 * Handles the management of a single file on the file system.
 * Can return detailed information on the file as well as moving and renaming.
 *
 * @package	mjohnson.transit
 */
class File {

	/**
	 * Cached values.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_cache = array();

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
	 * Return the file name with extension.
	 *
	 * @access public
	 * @return string
	 */
	public function basename() {
		return pathinfo($this->_path, PATHINFO_BASENAME);
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
		if (isset($this->_cache[__FUNCTION__])) {
			return $this->_cache[__FUNCTION__];
		}

		$data = @getimagesize($this->_path);
		$dims = null;

		if ($data && is_array($data)) {
			$dims = array(
				'width' => $data[0],
				'height' => $data[1]
			);
		}

		if (!$data) {
			$image = @imagecreatefromstring(file_get_contents($this->_path));
			$dims = array(
				'width' => @imagesx($image),
				'height' => @imagesy($image)
			);
		}

		$this->_cache[__FUNCTION__] = $dims;

		return $dims;
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
	 * Return the image height.
	 *
	 * @access public
	 * @return string
	 */
	public function height() {
		if (isset($this->_cache[__FUNCTION__])) {
			return $this->_cache[__FUNCTION__];
		}

		$height = 0;

		if ($dims = $this->dimensions()) {
			$height = $dims['height'];
		}

		$this->_cache[__FUNCTION__] = $height;

		return $height;
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
	 * @return string
	 */
	public function name() {
		return pathinfo($this->_path, PATHINFO_FILENAME);
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
		if (isset($this->_cache[__FUNCTION__])) {
			return $this->_cache[__FUNCTION__];
		}

		$f = finfo_open(FILEINFO_MIME);

		list($type, $charset) = explode(';', finfo_file($f, $this->_path));

		finfo_close($f);

		$this->_cache[__FUNCTION__] = $type;

		return $type;
	}

	/**
	 * Return the image width.
	 *
	 * @access public
	 * @return string
	 */
	public function width() {
		if (isset($this->_cache[__FUNCTION__])) {
			return $this->_cache[__FUNCTION__];
		}

		$width = 0;

		if ($dims = $this->dimensions()) {
			$width = $dims['width'];
		}

		$this->_cache[__FUNCTION__] = $width;

		return $width;
	}

}