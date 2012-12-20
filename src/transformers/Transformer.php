<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers;

/**
 * Interface for all transformers to implement.
 *
 * @package	mjohnson.transit.transformers
 */
interface Transformer {

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
	 * @param boolean $overwrite
	 * @return string
	 */
	public function transform($overwrite = false);

}