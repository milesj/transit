<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transporters;

use mjohnson\transit\File;

/**
 * Interface for all transporters to implement.
 *
 * @package	mjohnson.transit.transporters
 */
interface Transporter {

	/**
	 * Delete a file from the remote location.
	 *
	 * @access public
	 * @param string $path
	 * @param array $options
	 * @return boolean
	 * @throws \Exception
	 */
	public function delete($path, array $options = array());

	/**
	 * Transport the file to a remote location.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @param array $options
	 * @return string
	 * @throws \Exception
	 */
	public function transport(File $file, array $options = array());

}