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
 * Resizes an image to new dimensions.
 */
class ResizeTransformer extends AbstractImageTransformer {

	const WIDTH = 'width';
	const HEIGHT = 'height';

	/**
	 * Configuration.
	 *
	 * @var array
	 */
	protected $_config = array(
		'mode' => self::WIDTH,
		'width' => null,
		'height' => null,
		'quality' => 100,
		'expand' => false,
		'aspect' => true
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
		$baseWidth = $file->width();
		$baseHeight = $file->height();
		$width = $config['width'];
		$height = $config['height'];
		$newWidth = null;
		$newHeight = null;

		if (is_numeric($width) && !$height) {
			$height = round(($baseHeight / $baseWidth) * $width);

		} else if (is_numeric($height) && !$width) {
			$width = round(($baseWidth / $baseHeight) * $height);

		} else if (!is_numeric($height) && !is_numeric($width)) {
			throw new InvalidArgumentException('Invalid width and height for resize');
		}

		// Maintains the aspect ratio of the image
		if ($config['aspect']) {
			$widthScale = round($width / $baseWidth);
			$heightScale = round($height / $baseHeight);

			if (($config['mode'] === self::WIDTH && $widthScale < $heightScale) || ($config['mode'] === self::HEIGHT && $widthScale > $heightScale)) {
				$newWidth = $width;
				$newHeight = ($baseHeight * $newWidth) / $baseWidth;

			} else if (($config['mode'] === self::WIDTH && $widthScale > $heightScale) || ($config['mode'] === self::HEIGHT && $widthScale < $heightScale)) {
				$newHeight = $height;
				$newWidth = ($newHeight * $baseWidth) / $baseHeight;

			} else {
				$newWidth = $width;
				$newHeight = $height;
			}
		} else {
			$newWidth = $width;
			$newHeight = $height;
		}

		// Don't expand if we don't want it too
		if (!$config['expand']) {
			if ($newWidth > $baseWidth) {
				$newWidth = $baseWidth;
			}

			if ($newHeight > $baseHeight) {
				$newHeight = $baseHeight;
			}
		}

		return $this->_process($file, array(
			'dest_w'	=> $newWidth,
			'dest_h'	=> $newHeight,
			'quality'	=> $config['quality'],
			'overwrite'	=> $self
		));
	}

}