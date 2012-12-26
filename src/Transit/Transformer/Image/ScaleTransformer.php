<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;

/**
 * Scale the image based on a percentage.
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
	 * @param \Transit\File $file
	 * @param boolean $self
	 * @return \Transit\File
	 */
	public function transform(File $file, $self = false) {
		$config = $this->_config;
		$width = round($file->width() * $config['percent']);
		$height = round($file->height() * $config['percent']);

		return $this->_process($file, array(
			'dest_w'	=> $width,
			'dest_h'	=> $height,
			'quality'	=> $config['quality'],
			'overwrite'	=> $self
		));
	}

}