<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit;

use \Exception;

/**
 * Imports a file from a specific location. Either locally, remote or from a stream.
 *
 * @package	mjohnson.transit
 */
class ImportHandler extends AbstractHandler {

	/**
	 * Copy a local file to the temp directory and return a File object.
	 *
	 * @access public
	 * @param string $path
	 * @param boolean $overwrite
	 * @param boolean $delete
	 * @return \mjohnson\transit\File
	 * @throws \Exception
	 */
	public function fromLocal($path, $overwrite = true, $delete = false) {
		$file = new File($path);
		$target = $this->findTarget($file, $overwrite);

		if (copy($path, $target)) {
			if ($delete) {
				$file->delete();
			}

			return new File($target);
		}

		throw new Exception(sprintf('Failed to copy %s to new location.', $file->basename()));
	}

	/**
	 * Copy a remote file to the temp directory and return a File object.
	 *
	 * @access public
	 * @param string $url
	 * @param boolean $overwrite
	 * @return \mjohnson\transit\File
	 * @throws \Exception
	 */
	public function fromRemote($url, $overwrite = true) {
		if (!function_exists('curl_init')) {
			throw new Exception('The cURL module is required for remote file importing.');
		}

		// Create a file name based off the URL
		$name = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME);

		// Fetch the remote file
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($curl);
		curl_close($curl);

		// Save the file locally
		$target = $this->findTarget($name, $overwrite);

		if (file_put_contents($target, $response)) {
			return new File($target);
		}

		throw new Exception(sprintf('Failed to import %s from remote location.', basename($target)));
	}

	/**
	 * Copy a file from the input stream into the temp directory and return a File object.
	 * Primarily used for Javascript AJAX file uploads.
	 *
	 * @access public
	 * @param string $field
	 * @param boolean $overwrite
	 * @return \mjohnson\transit\File
	 * @throws \Exception
	 */
	public function fromStream($field, $overwrite = true) {
		if (empty($_GET[$field])) {
			throw new Exception(sprintf('%s was not found in the input stream.', $field));
		}

		$target = $this->findTarget($_GET[$field], $overwrite);
		$input = fopen('php://input', 'r');
		$output = fopen($target, 'w');

		stream_copy_to_stream($input, $output);

		fclose($input);
		fclose($output);

		return new File($target);
	}

}