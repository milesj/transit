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
 * Rotate an image to a certain degree.
 *
 * @package Transit\Transformer\Image
 */
class RotateTransformer extends AbstractImageTransformer {

    /**
     * Configuration.
     *
     * @type array {
     *         @type int $degrees    The degree amount to rotate with
     *         @type int $quality    Quality of JPEG image
     * }
     */
    protected $_config = array(
        'degrees' => 180,
        'quality' => 100
    );

    /**
     * {@inheritdoc}
     */
    public function transform(File $file, $self = false) {
        $config = $this->getConfig();

        return $this->_process($file, array(
            'dest_w'        => $file->width(),
            'dest_h'        => $file->height(),
            'quality'       => $config['quality'],
            'overwrite'     => $self,
            'target'        => sprintf('%s-rotate-%s', $file->name(), $config['degrees']),
            'postCallback'  => array($this, 'rotate')
        ));
    }

    /**
     * Rotate the image using the degrees option.
     *
     * @param resource $image
     * @return resource
     */
    public function rotate($image) {
        return imagerotate($image, $this->getConfig('degrees'), 0);
    }

}
