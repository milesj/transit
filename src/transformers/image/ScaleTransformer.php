<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers\image;

/**
 * Scale the image based on a percentage.
 *
 * @package	mjohnson.transit.transformers.image
 */
class ScaleTransformer extends AbstractImageTransformer {

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
	 * @param boolean $self
	 * @return \mjohnson\transit\File
	 */
	public function transform($self = false) {
		$config = $this->_config;
		$width = round($this->getFile()->width() * $config['percent']);
		$height = round($this->getFile()->height() * $config['percent']);

		return $this->process(array(
			'dest_w'	=> $width,
			'dest_h'	=> $height,
			'quality'	=> $config['quality'],
			'overwrite'	=> $self
		));
	}

}