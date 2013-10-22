<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Validator;

use Transit\Component\AbstractComponent;
use Transit\File;
use Transit\MimeType;
use Transit\Validator;
use Transit\Exception\IoException;
use Transit\Exception\ValidationException;
use \BadMethodCallException;

/**
 * Provides basic file validation functionality.
 *
 * @package Transit\Validator
 */
abstract class AbstractValidator extends AbstractComponent implements Validator {

    /**
     * File object.
     *
     * @type \Transit\File
     */
    protected $_file;

    /**
     * Validation rules.
     *
     * @type array
     */
    protected $_rules = array();

    /**
     * {@inheritdoc}
     */
    public function addRule($method, $message, $params = array()) {
        $this->_rules[] = array(
            'method' => $method,
            'message' => (string) $message,
            'params' => (array) $params
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile() {
        return $this->_file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(File $file) {
        $this->_file = $file;

        return $this;
    }

    /**
     * Validate file size is less than or equal to the max.
     *
     * @param int $max
     * @return bool
     */
    public function size($max) {
        return ($this->getFile()->size() <= $max);
    }

    /**
     * Validate the extension is in the whitelist.
     *
     * @param array $whitelist
     * @return bool
     */
    public function ext($whitelist = array()) {
        return in_array($this->getFile()->ext(), (array) $whitelist);
    }

    /**
     * Validate the mime type is in the whitelist, e.g., image/jpeg.
     *
     * @param array $whitelist
     * @return bool
     */
    public function mimeType($whitelist = array()) {
        return in_array($this->getFile()->type(), (array) $whitelist);
    }

    /**
     * Validate the top-level type of file, e.g., image.
     *
     * @uses Transit\MimeType
     *
     * @param string|array $mimeTypes
     * @return bool
     */
    public function type($mimeTypes) {
        $types = array();

        foreach ((array) $mimeTypes as $mimeType) {
            switch ($mimeType) {
                case 'application': $types += MimeType::getApplicationList(); break;
                case 'audio':       $types += MimeType::getAudioList(); break;
                case 'image':       $types += MimeType::getImageList(); break;
                case 'text':        $types += MimeType::getTextList(); break;
                case 'video':       $types += MimeType::getVideoList(); break;
                default:            $types += MimeType::getSubTypeList($mimeType); break;
            }
        }

        return in_array($this->getFile()->type(), $types);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Transit\Exception\IoException
     * @throws \Transit\Exception\ValidationException
     * @throws \BadMethodCallException
     */
    public function validate() {
        if (!$this->_rules) {
            return true;
        }

        if (!$this->_file) {
            throw new IoException('No file present for validation');
        }

        foreach ($this->_rules as $rule) {
            $method = $rule['method'];

            if (!method_exists($this, $method)) {
                throw new BadMethodCallException(sprintf('Validation method %s does not exist', $method));
            }

            if (!call_user_func_array(array($this, $method), $rule['params'])) {
                throw new ValidationException($rule['message']);
            }
        }

        return true;
    }

}