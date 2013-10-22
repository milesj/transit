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
 * Flips an image in 3 possible directions: vertical, horizontal, or both.
 *
 * @package Transit\Transformer\Image
 */
class FlipTransformer extends AbstractImageTransformer {

    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';
    const BOTH = 'both';

    /**
     * Configuration.
     *
     * @type array {
     *         @type string $direction  Direction to flip the image
     *         @type int $quality       Quality of JPEG image
     * }
     */
    protected $_config = array(
        'direction' => self::VERTICAL,
        'quality' => 100
    );

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function transform(File $file, $self = false) {
        $config = $this->getConfig();
        $width = $file->width();
        $height = $file->height();
        $src_x = 0;
        $src_y = 0;
        $src_w = $width;
        $src_h = $height;

        switch ($config['direction']) {
            case self::VERTICAL:
                $src_y = $height;
                $src_h = -$height;
            break;
            case self::HORIZONTAL:
                $src_x = $width;
                $src_w = -$width;
            break;
            case self::BOTH:
                $src_x = $width;
                $src_y = $height;
                $src_w = -$width;
                $src_h = -$height;
            break;
            default:
                throw new InvalidArgumentException(sprintf('Invalid flip direction %s', $config['direction']));
            break;
        }

        return $this->_process($file, array(
            'dest_w'    => $width,
            'dest_h'    => $height,
            'source_x'  => $src_x,
            'source_y'  => $src_y,
            'source_w'  => $src_w,
            'source_h'  => $src_h,
            'quality'   => $config['quality'],
            'overwrite' => $self,
            'target'    => sprintf('%s-flip-%s', $file->name(), $config['direction'])
        ));
    }

}