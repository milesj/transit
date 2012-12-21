<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\handlers;

use mjohnson\transit\File;

/**
 * Base class the handles shared functionality.
 *
 * @package	mjohnson.transit.handlers
 */
abstract class AbstractHandler {

	/**
	 * Temp directory.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_directory = __DIR__;

	/**
	 * Find a valid target path taking into account file existence and overwriting.
	 *
	 * @access public
	 * @param \mjohnson\transit\File|string $file
	 * @param boolean $overwrite
	 * @return string
	 */
	public function findTarget($file, $overwrite = false) {
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
	 * Set the temporary directory and create it if it doesn't exist.
	 *
	 * @access public
	 * @param string $path
	 * @return \mjohnson\transit\Importer
	 */
	public function setDirectory($path) {
		if (substr($path, -1) != '/') {
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

}