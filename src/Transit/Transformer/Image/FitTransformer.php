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
         *              @type mixed $fill               Fill bounds with given (rgb) color or don't
         *              @type string $verticalAlign
         *              @type string $horizontalAlign
	 * }
	 */
	protected $_config = array(
		'maxWidth' => null,
		'maxHeight' => null,
		'quality' => 100,
		'expand' => false,
                'fill' => false,
                'vericalAlign' => 'center',
                'horizontalAlign' => 'center'
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

                if($config['fill']) {

                    if(!in_array($config['verticalAlign'], array('top','center', 'bottom')))
                        throw new InvalidArgumentException('Invalid verticalAlign argument');

                    if(!in_array($config['horizontalAlign'], array('left','center', 'right')))
                        throw new InvalidArgumentException('Invalid horizontalAlign argument');

                    if(count($config['fill'])!= 3)
                        throw new InvalidArgumentException('Invalid color definition in fill');

                    foreach ($config['fill'] as $clr)
                        if(!is_numeric($clr) || ($clr < 0) || ($clr > 255))
                            throw new InvalidArgumentException('Invalid color definition in fill');
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

                if(!$config['fill'] || (($newHeight == $maxHeight) && ($newWidth == $maxWidth))) {
                    return $this->_process($file, array(
                            'dest_w'	=> $newWidth,
                            'dest_h'	=> $newHeight,
                            'quality'	=> $config['quality'],
                            'overwrite'	=> $self
                    ));
                }

                return $this->_process($file, array(
                        'dest_w'	=> $newWidth,
                        'dest_h'	=> $newHeight,
                        'quality'	=> $config['quality'],
                        'overwrite'	=> $self,
                        'callback'      => array($this, 'fillBounds')
                ));
	}

        public function fillBounds($image) {
            $config = $this->getConfig();

            // FIXME: I'm not sure about transparency in PNG or GIF files
            $img = imagecreatetruecolor($config['maxWidth'], $config['maxHeight']);

            $sourceWidth = imagesx($image);
            $sourceHeight = imagesy($image);

            $color = imagecolorallocate($img, $config['fill'][0], $config['fill'][1], $config['fill'][2]);

            imagefill($img, 0, 0, $color);

            if($sourceWidth < $config['maxWidth']) {
                switch ($config['horizontalAlign']) {
                    case 'center': $dst_x = (int)floor(($config['maxWidth'] - $sourceWidth)/2);break;
                    case 'right': $dst_x = $config['maxWidth'] - $sourceWidth;break;
                    case 'left':
                    default:
                        $dst_x = 0;
                        break;
                }
            } else {
                $dst_x = 0;
            }

            if($sourceHeight < $config['maxHeight']) {
                switch ($config['verticalAlign']) {
                    case 'center': $dst_y = (int)floor(($config['maxHeight'] - $sourceHeight)/2);break;
                    case 'bottom': $dst_y = $config['maxHeight'] - $sourceHeight;break;
                    case 'top':
                    default:
                        $dst_y = 0;
                        break;
                }
            } else {
                $dst_y = 0;
            }

            imagecopy($img, $image, $dst_x, $dst_y, 0, 0, $sourceWidth, $sourceHeight);

            return $img;
        }

}