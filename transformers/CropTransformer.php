<?php

namespace mjohnson\transit\transformers;

/**
 * Crops a photo, but resizes and keeps aspect ratio depending on which side is larger.
 */
class CropTransformer extends TransformerAbstract {

	const TOP = 'top';
	const BOTTOM = 'bottom';
	const LEFT = 'left';
	const RIGHT = 'right';
	const CENTER = 'center';

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'location' => self::CENTER,
		'quality' => 100,
		'width' => null,
		'height' => null
	);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @return string
	 */
	public function transform() {
		$options = $this->_config;
		$baseWidth = $this->_width;
		$baseHeight = $this->_height;
		$width = $options['width'];
		$height = $options['height'];

		if (is_numeric($width) && !$height) {
			$height = round(($baseHeight / $baseWidth) * $width);

		} else if (is_numeric($height) && !$width) {
			$width = round(($baseWidth / $baseHeight) * $height);

		} else if (!is_numeric($height) && !is_numeric($width)) {
			return null;
		}

		$location = $options['location'];
		$widthScale = $baseWidth / $width;
		$heightScale = $baseHeight / $height;
		$src_x = 0;
		$src_y = 0;
		$src_w = $baseWidth;
		$src_h = $baseHeight;

		// Source width is larger, use height scale as the base
		if ($widthScale > $heightScale) {
			$src_w = round($width * $heightScale);

			// Position horizontally in the middle
			if ($location === self::CENTER) {
				$src_x = round(($baseWidth / 2) - (($width / 2) * $heightScale));

			// Position at the far right
			} else if ($location === self::RIGHT || $location === self::BOTTOM) {
				$src_x = $baseWidth - $src_w;
			}

		// Source height is larger, use width scale as the base
		} else {
			$src_h = round($height * $widthScale);

			// Position vertically in the middle
			if ($location === self::CENTER) {
				$src_y = round(($baseHeight / 2) - (($height / 2) * $widthScale));

			// Position at the bottom
			} else if ($location === self::RIGHT || $location === self::BOTTOM) {
				$src_y = $baseHeight - $src_h;
			}
		}

		return $this->process(array(
			'dest_w'	=> $width,
			'dest_h'	=> $height,
			'source_x'	=> $src_x,
			'source_y'	=> $src_y,
			'source_w'	=> $src_w,
			'source_h'	=> $src_h,
			'quality'	=> $options['quality']
		));
	}

}