<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit;

use mjohnson\transit\exceptions\IoException;
use \Closure;

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
	 * @throws \mjohnson\transit\exceptions\IoException
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
		return $this->_cache(__FUNCTION__, function() {
			$dims = null;

			if (!$this->isImage()) {
				return $dims;
			}

			$data = @getimagesize($this->_path);

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
		return $this->_cache(__FUNCTION__, function() {
			if (!$this->isImage()) {
				return null;
			}

			$height = 0;

			if ($dims = $this->dimensions()) {
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
	public function isApp() {
		return $this->_is('app');
	}

	/**
	 * Return true if the file is an archive.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isArchive() {
		return $this->_is('archive');
	}

	/**
	 * Return true if the file is an audio.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isAudio() {
		return $this->_is('audio');
	}

	/**
	 * Return true if the file is an image.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isImage() {
		return $this->_is('image');
	}

	/**
	 * Return true if the file is a text.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isText() {
		return $this->_is('text');
	}

	/**
	 * Return true if the file is a video.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isVideo() {
		return $this->_is('video');
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
		return $this->_cache(__FUNCTION__, function() {
			$file = finfo_open(FILEINFO_MIME_TYPE);
			$type = finfo_file($file, $this->_path);
			finfo_close($file);

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
		return $this->_cache(__FUNCTION__, function() {
			if (!$this->isImage()) {
				return null;
			}

			$width = 0;

			if ($dims = $this->dimensions()) {
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
			'directory' => $this->dir(),
			'extension' => $this->ext(),
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
	 * @param callable $callback
	 * @return mixed
	 */
	protected function _cache($key, Closure $callback) {
		if (isset($this->_cache[$key])) {
			return $this->_cache[$key];
		}

		Closure::bind($callback, $this, __CLASS__);

		$this->_cache[$key] = $callback();

		return $this->_cache[$key];
	}

	/**
	 * Return true if the grouping is found within the mime type.
	 *
	 * @access protected
	 * @param string $type
	 * @return boolean
	 */
	protected function _is($type) {
		return (strpos($this->type(), $type) === 0);
	}

}