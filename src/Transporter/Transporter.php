<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transporter;

use Transit\File;

/**
 * Interface for all transporters to implement.
 */
interface Transporter {

	/**
	 * Delete a file from the remote location.
	 *
	 * @access public
	 * @param string $id
	 * @return boolean
	 */
	public function delete($id);

	/**
	 * Transport the file to a remote location.
	 *
	 * @access public
	 * @param \Transit\File $file
	 * @return string
	 */
	public function transport(File $file);

}