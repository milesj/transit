<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;

/**
 * Rotates, flips and fixes an images orientation based on exif data.
 * Will also remove exif data after processing.
 *
 * @package Transit\Transformer\Image
 */
class ExifTransformer extends RotateTransformer {

    /**
     * {@inheritdoc}
     */
    public function transform(File $file, $self = false) {
        if ($file->type() !== 'image/jpeg') {
            return $file; // Exif only in JPGs
        }

        $width = $file->width();
        $height = $file->height();
        $exif = $file->exif();
        $this->setConfig('degrees', 0); // Reset degrees

        $options = array(
            'dest_w'    => $width,
            'dest_h'    => $height,
            'source_w'  => $width,
            'source_h'  => $height,
            'quality'   => $this->getConfig('quality'),
            'overwrite' => $self,
            'target'    => sprintf('%s-exif-%s', $file->name(), $exif['orientation'] ?: 0)
        );

        switch ($exif['orientation']) {
            case 2:
                // Flip horizontally
                $options['source_x'] = $width;
                $options['source_w'] = -$width;
            break;
            case 3:
                // Rotate 180 degrees
                $this->setConfig('degrees', 180);
            break;
            case 4:
            case 5:
            case 7:
                // Flip vertically
                $options['source_y'] = $height;
                $options['source_h'] = -$height;

                // Also rotate -90 degrees for orientation 5
                if ($exif['orientation'] == 5) {
                    $this->setConfig('degrees', -90);
                }

                // Or rotate 90 degrees for orientation 7
                if ($exif['orientation'] == 7) {
                    $this->setConfig('degrees', 90);
                }
            break;
            case 6:
                $this->setConfig('degrees', -90);
            break;
            case 8:
                $this->setConfig('degrees', 90);
            break;
            default:
                // Correct, strip exif only
            break;
        }

        if ($degrees = $this->getConfig('degrees')) {
            $options['postCallback'] = array($this, 'rotate');
        }

        return $this->_process($file, $options);
    }

}