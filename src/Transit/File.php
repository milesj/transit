<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\Exception\IoException;
use \InvalidArgumentException;
use \RuntimeException;
use \Closure;

/**
 * Handles the management of a single file on the file system.
 * Can return detailed information on the file as well as moving and renaming.
 *
 * @package Transit
 */
class File {

    /**
     * Cached values.
     *
     * @type array
     */
    protected $_cache = array();

    /**
     * Raw $_FILES data.
     *
     * @type array
     */
    protected $_data = array();

    /**
     * Absolute file path.
     *
     * @type string
     */
    protected $_path;

    /**
     * Store the file path.
     *
     * @param string|array $path
     * @throws \Transit\Exception\IoException
     */
    public function __construct($path) {
        if (is_array($path)) {
            if (empty($path['tmp_name'])) {
                throw new IoException('Passing via array must use $_FILES data');
            }

            $this->_data = $path;
            $path = $path['tmp_name'];
        }

        if (!file_exists($path)) {
            throw new IoException(sprintf('%s does not exist', $path));
        }

        $this->_path = $path;

        // @version 1.3.2 Rename file to add ext if ext is missing
        if (!$this->ext()) {
            $this->rename();
        }

        // @version 1.4.0 Reset the cache
        $this->_cache = array();
    }

    /**
     * Magic method for toString().
     *
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Return the file name with extension.
     *
     * @return string
     */
    public function basename() {
        // @version 1.2.0 Use filename then fallback to path
        $path = $this->data('name') ?: $this->path();

        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Return the $_FILES data.
     *
     * @param string $key
     * @return string
     */
    public function data($key) {
        return !empty($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Delete the file.
     *
     * @return bool
     */
    public function delete() {
        $this->reset();

        return @unlink($this->_path);
    }

    /**
     * Return the dimensions of the file if it is an image.
     *
     * @return array
     */
    public function dimensions() {
        return $this->_cache(__FUNCTION__, function($file) {
            /** @type \Transit\File $file */

            $dims = null;

            if (!$file->isImage()) {
                return $dims;
            }

            $data = @getimagesize($file->path());

            if ($data && is_array($data)) {
                $dims = array(
                    'width' => $data[0],
                    'height' => $data[1]
                );
            }

            if (!$data) {
                $image = @imagecreatefromstring(file_get_contents($file->path()));
                $dims = array(
                    'width' => @imagesx($image),
                    'height' => @imagesy($image)
                );
            }

            return $dims;
        });
    }

    /**
     * Return the directory the file is in.
     *
     * @return string
     */
    public function dir() {
        return dirname($this->_path) . '/';
    }

    /**
     * Attempt to read and determine correct exif data.
     *
     * @param array $fields
     * @returns array
     */
    public function exif(array $fields = array()) {
        if (!function_exists('exif_read_data')) {
            return array();
        }

        return $this->_cache(__FUNCTION__, function($file) use ($fields) {
            /** @type \Transit\File $file */

            $exif = array();
            $fields = $fields + array(
                'make' => 'Make',
                'model' => 'Model',
                'exposure' => 'ExposureTime',
                'orientation' => 'Orientation',
                'fnumber' => 'FNumber',
                'date' => 'DateTime',
                'iso' => 'ISOSpeedRatings',
                'focal' => 'FocalLength'
            );

            if ($file->supportsExif()) {
                if ($data = @exif_read_data($file->path())) {
                    foreach ($fields as $key => $find) {
                        $value = '';

                        if (!empty($data[$find])) {
                            $value = $data[$find];
                        }

                        $exif[$key] = $value;
                    }
                }
            }

            // Return empty values for files that don't support exif
            if (!$exif) {
                $exif = array_map(function() {
                    return '';
                }, $fields);
            }

            return $exif;
        });
    }

    /**
     * Return the extension.
     *
     * @return string
     */
    public function ext() {
        return $this->_cache(__FUNCTION__, function($file) {
            /** @type \Transit\File $file */

            // @version 1.1.1 Removed because of fileinfo bug
            // return MimeType::getExtFromType($file->type(), true);

            // @version 1.2.0 Allow support for $_FILES array
            $path = $file->data('name') ?: $file->path();

            return mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
        });
    }

    /**
     * Return the image height.
     *
     * @return int
     */
    public function height() {
        return $this->_cache(__FUNCTION__, function($file) {
            /** @type \Transit\File $file */

            if (!$file->isImage()) {
                return null;
            }

            $height = 0;

            if ($dims = $file->dimensions()) {
                $height = $dims['height'];
            }

            return $height;
        });
    }

    /**
     * Return true if the file is an application.
     *
     * @uses Transit\MimeType
     *
     * @return bool
     */
    public function isApplication() {
        return MimeType::isApplication($this);
    }

    /**
     * Return true if the file is an audio.
     *
     * @uses Transit\MimeType
     *
     * @return bool
     */
    public function isAudio() {
        return MimeType::isAudio($this);
    }

    /**
     * Return true if the file is an image.
     *
     * @uses Transit\MimeType
     *
     * @return bool
     */
    public function isImage() {
        return MimeType::isImage($this);
    }

    /**
     * Return true if the file is a text.
     *
     * @uses Transit\MimeType
     *
     * @return bool
     */
    public function isText() {
        return MimeType::isText($this);
    }

    /**
     * Return true if the file is a video.
     *
     * @uses Transit\MimeType
     *
     * @return bool
     */
    public function isVideo() {
        return MimeType::isVideo($this);
    }

    /**
     * Return true if the file is part of a sub-type.
     *
     * @uses Transit\MimeType
     *
     * @param string $subType
     * @return bool
     */
    public function isSubType($subType) {
        return MimeType::isSubType($subType, $this);
    }

    /**
     * Move the file to a new directory.
     * If a file with the same name already exists, either overwrite or increment file name.
     *
     * @param string $path
     * @param bool $overwrite
     * @return bool
     */
    public function move($path, $overwrite = false) {
        $path = str_replace('\\', '/', $path);

        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        // Don't move to the same folder
        if (realpath($path) === realpath($this->dir())) {
            return true;
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);

        } else if (!is_writable($path)) {
            chmod($path, 0777);
        }

        // Determine name and overwrite
        $name = $this->name();
        $ext = $this->ext();

        if (!$overwrite) {
            $no = 1;

            while (file_exists($path . $name . '.' . $ext)) {
                $name = $this->name() . '-' . $no;
                $no++;
            }
        }

        // Move the file
        $targetPath = $path . $name . '.' . $ext;

        if (rename($this->path(), $targetPath)) {
            $this->reset($targetPath);

            return true;
        }

        return false;
    }

    /**
     * Return the file name without extension.
     *
     * @return string
     */
    public function name() {
        // @version 1.2.0 Use filename then fallback to path
        $path = $this->data('name') ?: $this->path();

        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Return the absolute path.
     *
     * @return string
     */
    public function path() {
        return $this->_path;
    }

    /**
     * Rename the file within the current directory.
     *
     * @param string $name
     * @param string $append
     * @param string $prepend
     * @return bool
     */
    public function rename($name = '', $append = '', $prepend = '') {
        if (is_callable($name)) {
            $name = call_user_func_array($name, array($this->name(), $this));
        } else {
            $name = $name ?: $this->name();
        }

        // Add boundaries
        $name = (string) $prepend . $name . (string) $append;

        // Remove unwanted characters
        $name = preg_replace('/[^_-\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/imu', '-', $name);

        // Rename file
        $ext = $this->ext() ?: MimeType::getExtFromType($this->type(), true);
        $targetPath = $this->dir() . $name . '.' . $ext;

        if (rename($this->path(), $targetPath)) {
            $this->reset($targetPath);

            return true;
        }

        return false;
    }

    /**
     * Reset all cache.
     *
     * @param string $path
     * @return \Transit\File
     */
    public function reset($path = '') {
        clearstatcache();

        $this->_cache = array();

        if ($path) {
            $this->_data['name'] = basename($path);
            $this->_path = $path;
        }

        return $this;
    }

    /**
     * Return the file size.
     *
     * @return int
     */
    public function size() {
        return filesize($this->_path);
    }

    /**
     * Checks if the file supports exif data.
     *
     * @return bool
     */
    public function supportsExif() {
        return $this->_cache(__FUNCTION__, function($file) {
            /** @type \Transit\File $file */

            if (!$file->isImage()) {
                return false;
            }

            return in_array($file->type(), array('image/jpeg', 'image/tiff'));
        });
    }

    /**
     * Return the mime type.
     *
     * @uses Transit\MimeType
     *
     * @return string
     */
    public function type() {
        return $this->_cache(__FUNCTION__, function($file) {
            /** @type \Transit\File $file */

            $type = null;

            // We can't use the file command on windows
            if (!defined('PHP_WINDOWS_VERSION_MAJOR')) {
                $type = shell_exec(sprintf("file -b --mime %s", escapeshellarg($file->path())));

                if ($type && strpos($type, ';') !== false) {
                    $type = strstr($type, ';', true);
                }
            }

            // Fallback because of fileinfo bug: https://bugs.php.net/bug.php?id=53035
            if (!$type) {
                $info = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($info, $file->path());
                finfo_close($info);
            }

            // Check the mimetype against the extension
            // If they are different, use the extension since fileinfo returns invalid mimetypes
            // This could be problematic in the future, but unknown better alternative
            if ($ext = $file->ext()) {
                try {
                    $extType = MimeType::getTypeFromExt($ext);

                // Use $_FILES['type'] last since sometimes it returns application/octet-stream and other mimetypes
                // When we should always have a true mimetype
                } catch (InvalidArgumentException $e) {
                    $extType = $file->data('type');
                }

                if (!empty($extType) && $type !== $extType) {
                    $type = $extType;
                }
            }

            return $type;
        });
    }

    /**
     * Return the image width.
     *
     * @return int
     */
    public function width() {
        return $this->_cache(__FUNCTION__, function($file) {
            /** @type \Transit\File $file */

            if (!$file->isImage()) {
                return null;
            }

            $width = 0;

            if ($dims = $file->dimensions()) {
                $width = $dims['width'];
            }

            return $width;
        });
    }

    /**
     * Return all File information as an array.
     *
     * @return array
     */
    public function toArray() {
        $data = array(
            'basename' => $this->basename(),
            'dir' => $this->dir(),
            'ext' => $this->ext(),
            'name' => $this->name(),
            'path' => $this->path(),
            'size' => $this->size(),
            'type' => $this->type(),
            'height' => $this->height(),
            'width' => $this->width()
        );

        // Include exif data
        foreach ($this->exif() as $key => $value) {
            $data['exif.' . $key] = $value;
        }

        return $data;
    }

    /**
     * Return path when cast to string.
     *
     * @return string
     */
    public function toString() {
        return $this->path();
    }

    /**
     * Cache the results of a callback.
     *
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     */
    protected function _cache($key, Closure $callback) {
        if (isset($this->_cache[$key])) {
            return $this->_cache[$key];
        }

        // Requires 5.4
        // Closure::bind($callback, $this, __CLASS__);

        $this->_cache[$key] = $callback($this);

        return $this->_cache[$key];
    }

}
