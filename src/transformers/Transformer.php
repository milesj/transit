<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers;

use mjohnson\transit\File;

/**
 * Interface for all transformers to implement.
 *
 * @package	mjohnson.transit.transformers
 */
interface Transformer {

	/**
	 * Return the File object.
	 *
	 * @access public
	 * @return \mjohnson\transit\File
	 */
	public function getFile();

	/**
	 * Set the File object.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @return \mjohnson\transit\transformers\Transformer
	 */
	public function setFile(File $file);

	/**
	 * Transform the image using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return \mjohnson\transit\File
	 */
	public function process(array $options);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @param boolean $self
	 * @return \mjohnson\transit\File
	 */
	public function transform($self = false);

}