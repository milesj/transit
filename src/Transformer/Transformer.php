<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer;

use Transit\File;

/**
 * Interface for all transformers to implement.
 */
interface Transformer {

	/**
	 * Return the File object.
	 *
	 * @access public
	 * @return \Transit\File
	 */
	public function getFile();

	/**
	 * Set the File object.
	 *
	 * @access public
	 * @param \Transit\File $file
	 * @return \Transit\Transformer\Transformer
	 */
	public function setFile(File $file);

	/**
	 * Transform the file using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return \Transit\File
	 */
	public function process(array $options);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @param boolean $self
	 * @return \Transit\File
	 */
	public function transform($self = false);

}