<?php
/**
 * Based on the code of ResizeTransformer by Miles Johnson
 *
 * @copyright	Copyright 2013, Serge Rodovnichenko - http://www.handmadesite.net
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use \InvalidArgumentException;

/**
 * Resizes an image to new dimensions.
 *
 * @package Transit\Transformer\Image
 */
class FitTransformer extends AbstractImageTransformer {

	/**
	 * Configuration.
	 *
	 * @type array {
	 * 		@type int $quality		Quality of JPEG image
	 * 		@type int $maxWidth		Width of output image
	 * 		@type int $maxHeight		Height of output image
	 * 		@type bool $expand		Allow image to be resized larger than the base dimensions
	 * }
	 */
	protected $_config = array(
		'maxWidth' => null,
		'maxHeight' => null,
		'quality' => 100,
		'expand' => false,
	);

	/**
	 * {@inheritdoc}
	 *
	 * @throws \InvalidArgumentException
	 */
	public function transform(File $file, $self = false) {
		$config = $this->getConfig();
		$baseWidth = $file->width();
		$baseHeight = $file->height();
		$maxWidth = $config['maxWidth'];
		$maxHeight = $config['maxHeight'];
		$newWidth = null;
		$newHeight = null;

		if (!is_numeric($maxWidth) || !is_numeric($maxHeight)) {
			throw new InvalidArgumentException('Invalid maxWidth or maxHeight for fit');
                }

                $heightAspect = $baseHeight / $maxHeight;
                $widthAspect = $baseWidth / $maxWidth;

                if (!$config['expand'] && ($maxWidth > $baseWidth) && ($maxHeight > $baseHeight)) {
                        $newWidth = $baseWidth;
                        $newHeight = $baseHeight;
                } else {

                    $aspect = $heightAspect > $widthAspect ? $heightAspect : $widthAspect;

                    $newWidth = $baseWidth / $aspect;
                    $newHeight = $baseHeight / $aspect;
                }

		return $this->_process($file, array(
			'dest_w'	=> $newWidth,
			'dest_h'	=> $newHeight,
			'quality'	=> $config['quality'],
			'overwrite'	=> $self
		));
	}

}