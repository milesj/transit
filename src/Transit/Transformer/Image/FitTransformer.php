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
         *              @type mixed $fill               Fill bounds with given (rgb) color or don't
         *              @type string $verticalAlign
         *              @type string $horizontalAlign
	 * }
	 */
	protected $_config = array(
		'maxWidth' => null,
		'maxHeight' => null,
		'quality' => 100,
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

                    if(!isset($config['verticalAlign']))
                        throw new InvalidArgumentException('Invalid verticalAlign argument');

                    if(!isset($config['horizontalAlign']))
                        throw new InvalidArgumentException('Invalid horizontalAlign argument');

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

                $aspect = $heightAspect > $widthAspect ? $heightAspect : $widthAspect;

                $newWidth = $baseWidth / $aspect;
                $newHeight = $baseHeight / $aspect;

                if(!$config['fill'] || (($newHeight == $maxHeight) && ($newWidth == $maxWidth))) {
                    return $this->_process($file, array(
                            'dest_w'	=> $newWidth,
                            'dest_h'	=> $newHeight,
                            'quality'	=> $config['quality'],
                            'overwrite'	=> $self
                    ));
                }

                return $this->_process($file, array(
                        'dest_w'	=> $maxWidth,
                        'dest_h'	=> $maxHeight,
                        'quality'	=> $config['quality'],
                        'overwrite'	=> $self,
                        'callback'      => array($this, 'fillBounds'),
                        'fill'		=> $config['fill'],
                        'actualHeight'	=> $newHeight,
                        'actualWidth'	=> $newWidth,
                        'horizontalAlign' => $config['horizontalAlign'],
                        'verticalAlign' => $config['verticalAlign']
                ));
	}

        public function fillBounds($image, $options) {

            $color = imagecolorallocate($image, $options['fill'][0], $options['fill'][1], $options['fill'][2]);

            if($options['actualWidth'] < $options['dest_w']) {
        	$gap_x = $options['dest_w'] - $options['actualWidth'];
                switch ($options['horizontalAlign']) {
                    case 'center':
                	$gap_x = (int)floor($gap_x/2);
                	imagefilledrectangle($image, 0, 0, $gap_x, $options['dest_h'], $color);
                	imagefilledrectangle($image, $gap_x+$options['actualWidth'], 0, $options['dest_w'], $options['dest_h'], $color); 
                	break;
                    
                    case 'right':
                	imagefilledrectangle($image, 0, 0, $gap_x, $options['dest_h'], $color);
                	break;
                	
                    case 'left':
                    default:
                	imagefilledrectangle($image, $options['actualWidth'], 0, $options['dest_w'], $options['dest_h'], $color);
                        break;
                }
            }

            if($options['actualHeight'] < $options['dest_h']) {
        	$gap_y = $options['dest_h']-$options['actualHeight'];
                switch ($options['verticalAlign']) {
                    case 'center':
                	$gap_y = (int)floor($gap_y/2);
                	imagefilledrectangle($image, 0, 0, $options['dest_w'], $gap_y, $color);
                	imagefilledrectangle($image, 0, $gap_y + $options['actualHeight'], $options['dest_w'], $options['dest_h'], $color);
                	break;
                	
                    case 'bottom':
                	imagefilledrectangle($image, 0, 0, $options['dest_w'], $gap_y, $color);
                	break;
                	
                    case 'top':
                    default:
                	imagefilledrectangle($image, 0, $options['actualHeight'], $options['dest_w'], $options['dest_h'], $color);
                        break;
                }
            }

            return $image;
        }

}