<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers;

/**
 * Scale the image based on a percentage.
 *
 * @package	mjohnson.transit.transformers
 */
class ScaleTransformer extends TransformerAbstract {

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'percent' => .5,
		'quality' => 100
	);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @param boolean $overwrite
	 * @return string
	 */
	public function transform($overwrite = false) {
		$config = $this->_config;
		$width = round($this->_file->width() * $config['percent']);
		$height = round($this->_file->height() * $config['percent']);

		return $this->process(array(
			'dest_w'	=> $width,
			'dest_h'	=> $height,
			'quality'	=> $config['quality'],
			'overwrite'	=> $overwrite
		));
	}

}