<?php
namespace mjohnson\transit;

use \Exception;

class Transit {

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
	 * @param boolean $overwrite
	 * @param string $name
	 * @return string
	 */
	public function findTarget($overwrite, $name) {
		$ext = '';

		if ($pos = mb_strrpos($name, '.')) {
			$length = mb_strlen($name);
			$ext = mb_substr($name, $pos, ($length - $pos));
			$name = mb_substr($name, 0, $pos);
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