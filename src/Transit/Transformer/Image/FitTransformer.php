<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use \InvalidArgumentException;

/**
 * Fit an image into the exact dimensions defined.
 * Will fill the background gaps and align accordingly.
 *
 * @package Transit\Transformer\Image
 */
class FitTransformer extends CropTransformer {

    /**
     * Configuration.
     *
     * @type array {
     *         @type int $width             Width of output image
     *         @type int $height            Height of output image
     *         @type int $quality           Quality of JPEG image
     *         @type array $fill            Fill bounds with given (rgba) color or don't
     *         @type string $vertical       Where to align the image vertically
     *         @type string $horizontal     Where to align the image horizontally
     * }
     */
    protected $_config = array(
        'width' => null,
        'height' => null,
        'quality' => 100,
        'fill' => array(),
        'vertical' => self::CENTER,
        'horizontal' => self::CENTER
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
        $width = $config['width'];
        $height = $config['height'];
        $newWidth = null;
        $newHeight = null;

        if (!is_numeric($height) && !is_numeric($width)) {
            throw new InvalidArgumentException('Invalid width and height for resize');

        } else if (empty($config['fill'])) {
            throw new InvalidArgumentException('Invalid RGB fill color');
        }

        $newWidth = $baseWidth;
        $newHeight = $baseHeight;

        // Calculate aspect ratio
        $widthAspect = $baseWidth / $width;
        $heightAspect = $baseHeight / $height;
        $aspect = ($heightAspect > $widthAspect) ? $heightAspect : $widthAspect;

        // Resize if larger image
        // @version 1.4.5 Changed to not expand the image larger than its dimensions
        if ($newWidth > $width || $newHeight > $height) {
            $newWidth = $baseWidth / $aspect;
            $newHeight = $baseHeight / $aspect;
        }

        // Determine the alignment
        $vertGap = ($height - $newHeight) / 2;
        $horiGap = ($width - $newWidth) / 2;

        if ($newWidth < $width) {
            if ($config['horizontal'] === self::LEFT) {
                $horiGap = 0;
            } else if ($config['horizontal'] === self::RIGHT) {
                $horiGap = ($width - $newWidth);
            }
        }

        if ($newHeight < $height) {
            if ($config['vertical'] === self::TOP) {
                $vertGap = 0;
            } else if ($config['vertical'] === self::BOTTOM) {
                $vertGap = ($height - $newHeight);
            }
        }

        return $this->_process($file, array(
            'width'         => $width,
            'height'        => $height,
            'dest_x'        => $horiGap,
            'dest_y'        => $vertGap,
            'dest_w'        => $newWidth,
            'dest_h'        => $newHeight,
            'quality'       => $config['quality'],
            'overwrite'     => $self,
            'preCallback'   => array($this, 'fill')
        ));
    }

    /**
     * Fill the background with an RGB color.
     *
     * @param resource $image
     * @return resource
     */
    public function fill($image) {
        $fill = $this->getConfig('fill');
        $r = $fill[0];
        $g = $fill[1];
        $b = $fill[2];
        $a = isset($fill[3]) ? $fill[3] : 127;

        imagefill($image, 0, 0, imagecolorallocatealpha($image, $r, $g, $b, $a));

        return $image;
    }

}