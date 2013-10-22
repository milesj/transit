<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Validator;

/**
 * Provides file validation functionality.
 *
 * @package Transit\Validator
 */
class ImageValidator extends AbstractValidator {

    /**
     * Validate the image height matches the size.
     *
     * @param int $size
     * @return bool
     */
    public function height($size) {
        return ($this->getFile()->height() == $size);
    }

    /**
     * Validate the image width matches the size.
     *
     * @param int $size
     * @return bool
     */
    public function width($size) {
        return ($this->getFile()->width() == $size);
    }

    /**
     * Validate image height is less than or equal to the max.
     *
     * @param int $max
     * @return bool
     */
    public function maxHeight($max) {
        return ($this->getFile()->height() <= $max);
    }

    /**
     * Validate image width is less than or equal to the max.
     *
     * @param int $max
     * @return bool
     */
    public function maxWidth($max) {
        return ($this->getFile()->width() <= $max);
    }

    /**
     * Validate image height is greater than or equal to the minimum.
     *
     * @param int $min
     * @return bool
     */
    public function minHeight($min) {
        return ($this->getFile()->height() >= $min);
    }

    /**
     * Validate image width is greater than or equal to the minimum.
     *
     * @param int $min
     * @return bool
     */
    public function minWidth($min) {
        return ($this->getFile()->width() >= $min);
    }

}