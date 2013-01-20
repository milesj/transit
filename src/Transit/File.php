<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\Exception\IoException;
use \Closure;

/**
 * Handles the management of a single file on the file system.
 * Can return detailed information on the file as well as moving and renaming.
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
	 * @throws \Transit\Exception\IoException
	 */
	public function __construct($path) {
		if (!file_exists($path)) {
			throw new IoException(sprintf('%s does not exist', $path));
		}

		$this->_path = $path;
	}

	/**
	 * Magic method for toString().
	 *
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
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
		$this->reset();

		return @unlink($this->_path);
	}

	/**
	 * Return the dimensions of the file if it is an image.
	 *
	 * @access public
	 * @return array
	 */
	public function dimensions() {
		return $this->_cache(__FUNCTION__, function($file) {
			$dims = null;

			if (!$file->isImage()) {
				return $dims;
			}

			$data = @getimagesize($file->path());

			if ($data && is_array($data)) {
				$dims = array(
					'width' => $data[0],
					'height' => $data[1]
				);
			}

			if (!$data) {
				$image = @imagecreatefromstring(file_get_contents($file->path()));
				$dims = array(
					'width' => @imagesx($image),
					'height' => @imagesy($image)
				);
			}

			return $dims;
		});
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
	 * @return int
	 */
	public function height() {
		return $this->_cache(__FUNCTION__, function($file) {
			if (!$file->isImage()) {
				return null;
			}

			$height = 0;

			if ($dims = $file->dimensions()) {
				$height = $dims['height'];
			}

			return $height;
		});
	}

	/**
	 * Return true if the file is an application.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isApplication() {
		return MimeType::isApplication($this);
	}

	/**
	 * Return true if the file is an audio.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isAudio() {
		return MimeType::isAudio($this);
	}

	/**
	 * Return true if the file is an image.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isImage() {
		return MimeType::isImage($this);
	}

	/**
	 * Return true if the file is a text.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isText() {
		return MimeType::isText($this);
	}

	/**
	 * Return true if the file is a video.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isVideo() {
		return MimeType::isVideo($this);
	}

	/**
	 * Return true if the file is part of a sub-type.
	 *
	 * @access public
	 * @param string $subType
	 * @return boolean
	 */
	public function isSubType($subType) {
		return MimeType::isSubType($subType, $this);
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

		// Don't move to the same folder
		if (realpath($path) === realpath($this->dir())) {
			return true;
		}

		if (!file_exists($path)) {
			mkdir($path, 0777, true);

		} else if (!is_writable($path)) {
			chmod($path, 0777);
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
			$this->reset();
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
	public function rename($name = '', $append = '', $prepend = '') {
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
			$this->reset();
			$this->_path = $targetPath;

			return true;
		}

		return false;
	}

	/**
	 * Reset all cache.
	 *
	 * @access public
	 * @return \Transit\File
	 */
	public function reset() {
		clearstatcache();

		$this->_cache = array();

		return $this;
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
		return $this->_cache(__FUNCTION__, function($file) {
			$info = finfo_open(FILEINFO_MIME_TYPE);
			$type = finfo_file($info, $file->path());
			finfo_close($info);

			return $type;
		});
	}

	/**
	 * Return the image width.
	 *
	 * @access public
	 * @return int
	 */
	public function width() {
		return $this->_cache(__FUNCTION__, function($file) {
			if (!$file->isImage()) {
				return null;
			}

			$width = 0;

			if ($dims = $file->dimensions()) {
				$width = $dims['width'];
			}

			return $width;
		});
	}

	/**
	 * Return all File information as an array.
	 *
	 * @access public
	 * @return array
	 */
	public function toArray() {
		return array(
			'basename' => $this->basename(),
			'dir' => $this->dir(),
			'ext' => $this->ext(),
			'name' => $this->name(),
			'path' => $this->path(),
			'size' => $this->size(),
			'type' => $this->type(),
			'height' => $this->height(),
			'width' => $this->width()
		);
	}

	/**
	 * Return path when cast to string.
	 *
	 * @access public
	 * @return string
	 */
	public function toString() {
		return $this->path();
	}

	/**
	 * Cache the results of a callback.
	 *
	 * @access protected
	 * @param string $key
	 * @param \Closure $callback
	 * @return mixed
	 */
	protected function _cache($key, Closure $callback) {
		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		// Requires 5.4
		// Closure::bind($callback, $this, __CLASS__);

		$this->_cache[$key] = $callback($this);

		return $this->_cache[$key];
	}

}