<?php

namespace mjohnson\transit\transformers;

/**
 * Flips an image in 3 possible directions: vertical, horizontal, or both.
 */
class FlipTransformer extends TransformerAbstract {

	const VERTICAL = 'vertical';
	const HORIZONTAL = 'horizontal';
	const BOTH = 'both';

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'direction' => self::VERTICAL,
		'quality' => 100
	);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @return string
	 */
	public function transform() {
		$options = $this->_config;
		$width = $this->_width;
		$height = $this->_height;
		$src_x = 0;
		$src_y = 0;
		$src_w = $width;
		$src_h = $height;

		switch ($options['direction']) {
			case self::VERTICAL:
				$src_y = --$height;
				$src_h = -$height;
			break;
			case self::HORIZONTAL:
				$src_x = --$width;
				$src_w = -$width;
			break;
			case self::BOTH:
				$src_x = --$width;
				$src_y = --$height;
				$src_w = -$width;
				$src_h = -$height;
			break;
			default:
				return null;
			break;
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