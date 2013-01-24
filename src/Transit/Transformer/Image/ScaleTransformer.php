<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use \InvalidArgumentException;

/**
 * Scale the image based on a percentage.
 */
class ScaleTransformer extends AbstractImageTransformer {

	/**
	 * Configuration.
	 *
	 * @var array
	 */
	protected $_config = array(
		'percent' => .5,
		'quality' => 100
	);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @param \Transit\File $file
	 * @param bool $self
	 * @return \Transit\File
	 * @throws \InvalidArgumentException
	 */
	public function transform(File $file, $self = false) {
		$config = $this->_config;

		if (empty($config['percent']) || !is_numeric($config['percent'])) {
			throw new InvalidArgumentException('Invalid percent for scaling');
		}

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