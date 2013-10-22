<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\Transformer;
use Transit\Transporter;
use Transit\Validator;
use Transit\Exception\IoException;
use Transit\Exception\TransformationException;
use Transit\Exception\TransportationException;
use Transit\Exception\ValidationException;
use \Exception;
use \RuntimeException;
use \InvalidArgumentException;

/**
 * Primary class that handles all aspects of the uploading and importing process.
 * Furthermore provides support for file transformation and transportation.
 *
 * @package Transit
 */
class Transit {

    /**
     * Form files data or import URI.
     *
     * @type array
     */
    protected $_data;

    /**
     * Temp upload directory.
     *
     * @type string
     */
    protected $_directory = __DIR__;

    /**
     * File instance after successful upload or import.
     *
     * @type \Transit\File
     */
    protected $_file;

    /**
     * List of Files from transformation.
     *
     * @type \Transit\File[]
     */
    protected $_files = array();

    /**
     * List of Transformers to create files from the original file.
     *
     * @type \Transit\Transformer[]
     */
    protected $_transformers = array();

    /**
     * List of Transformers to apply to the original file.
     *
     * @type \Transit\Transformer[]
     */
    protected $_selfTransformers = array();

    /**
     * Transporter instance.
     *
     * @type \Transit\Transporter
     */
    protected $_transporter;

    /**
     * Validator instance.
     *
     * @type \Transit\Validator
     */
    protected $_validator;

    /**
     * Store $_FILES data or URI.
     *
     * @param array|string $data
     */
    public function __construct($data) {
        $this->_data = $data;
    }

    /**
     * Add a Transformer to generate new images with.
     *
     * @param \Transit\Transformer $transformer
     * @return \Transit\Transit
     */
    public function addTransformer(Transformer $transformer) {
        $this->_transformers[] = $transformer;

        return $this;
    }

    /**
     * Add a Transformer to apply to the original file.
     *
     * @param \Transit\Transformer $transformer
     * @return \Transit\Transit
     */
    public function addSelfTransformer(Transformer $transformer) {
        $this->_selfTransformers[] = $transformer;

        return $this;
    }

    /**
     * Find a valid target path taking into account file existence and overwriting.
     *
     * @param \Transit\File|string $file
     * @param bool $overwrite
     * @return string
     */
    public function findDestination($file, $overwrite = false) {
        if ($file instanceof File) {
            $name = $file->name();
            $ext = '.' . $file->ext();

        } else {
            $name = $file;
            $ext = '';

            if ($pos = mb_strrpos($name, '.')) {
                $ext = mb_substr($name, $pos, (mb_strlen($name) - $pos));
                $name = mb_substr($name, 0, $pos);
            }
        }

        $target = $this->_directory . $name . $ext;

        if (!$overwrite) {
            $no = 1;

            while (file_exists($target)) {
                $target = sprintf('%s%s-%s%s', $this->_directory, $name, $no, $ext);
                $no++;
            }
        }

        return $target;
    }

    /**
     * Return the file that was uploaded or imported.
     *
     * @return \Transit\File
     */
    public function getOriginalFile() {
        return $this->_file;
    }

    /**
     * Return a list of all transformed files.
     *
     * @return \Transit\File[]
     */
    public function getTransformedFiles() {
        return $this->_files;
    }

    /**
     * Return the original file and all transformed files.
     *
     * @return \Transit\File[]
     */
    public function getAllFiles() {
        return array_merge(array($this->getOriginalFile()), $this->getTransformedFiles());
    }

    /**
     * Return the Transporter object.
     *
     * @return \Transit\Transporter
     */
    public function getTransporter() {
        return $this->_transporter;
    }

    /**
     * Return the Validator object.
     *
     * @return \Transit\Validator
     */
    public function getValidator() {
        return $this->_validator;
    }

    /**
     * Copy a local file to the temp directory and return a File object.
     *
     * @param bool $overwrite
     * @param bool $delete
     * @return bool
     * @throws \Transit\Exception\IoException
     */
    public function importFromLocal($overwrite = true, $delete = false) {
        $path = $this->_data;
        $file = new File($path);
        $target = $this->findDestination($file, $overwrite);

        if (copy($path, $target)) {
            if ($delete) {
                $file->delete();
            }

            $this->_file = new File($target);

            return true;
        }

        throw new IoException(sprintf('Failed to copy %s to new location', $file->basename()));
    }

    /**
     * Copy a remote file to the temp directory and return a File object.
     *
     * @param bool $overwrite
     * @param array $options
     * @return bool
     * @throws \Transit\Exception\IoException
     * @throws \RuntimeException
     */
    public function importFromRemote($overwrite = true, array $options = array()) {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('The cURL module is required for remote file importing');
        }

        $url = $this->_data;
        $name = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME);

        if (!$name) {
            $name = md5(microtime(true));
        }

        $options = $options + array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYPEER => false
        );

        // Fetch the remote file
        $curl = curl_init($url);
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        // Save the file locally
        if (!$error) {
            $target = $this->findDestination($name, $overwrite);

            if (file_put_contents($target, $response)) {
                $this->_file = new File($target);

                return true;
            }
        }

        throw new IoException(sprintf('Failed to import %s from remote location: %s', $name, $error));
    }

    /**
     * Copy a file from the input stream into the temp directory and return a File object.
     * Primarily used for Javascript AJAX file uploads.
     *
     * @param bool $overwrite
     * @return bool
     * @throws \Transit\Exception\IoException
     */
    public function importFromStream($overwrite = true) {
        $target = $this->findDestination($this->_data, $overwrite);
        $input = fopen('php://input', 'r');
        $output = fopen($target, 'w');

        $size = stream_copy_to_stream($input, $output);

        fclose($input);
        fclose($output);

        if ($size <= 0) {
            @unlink($target);

            throw new IoException('No file detected in input stream');
        }

        $this->_file = new File($target);

        return $size;
    }

    /**
     * Rollback and delete all uploaded and transformed files.
     *
     * @return \Transit\Transit
     */
    public function rollback() {
        if ($files = $this->getAllFiles()) {
            foreach ($files as $file) {
                if ($file instanceof File) {
                    $file->delete();
                }
            }
        }

        $this->_file = null;
        $this->_files = array();

        return $this;
    }

    /**
     * Set the temporary directory and create it if it does not exist.
     *
     * @param string $path
     * @return \Transit\Transit
     */
    public function setDirectory($path) {
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);

        } else if (!is_writable($path)) {
            chmod($path, 0777);
        }

        $this->_directory = $path;

        return $this;
    }

    /**
     * Set the Transporter.
     *
     * @param \Transit\Transporter $transporter
     * @return \Transit\Transit
     */
    public function setTransporter(Transporter $transporter) {
        $this->_transporter = $transporter;

        return $this;
    }

    /**
     * Set the Validator.
     *
     * @param \Transit\Validator $validator
     * @return \Transit\Transit
     */
    public function setValidator(Validator $validator) {
        $this->_validator = $validator;

        return $this;
    }

    /**
     * Apply transformations to the original file and generate new transformed files.
     *
     * @return bool
     * @throws \Transit\Exception\IoException
     * @throws \Transit\Exception\TransformationException
     */
    public function transform() {
        $originalFile = $this->getOriginalFile();
        $transformedFiles = array();
        $error = null;

        if (!$originalFile) {
            throw new IoException('No original file detected');
        }

        // Apply transformations to original
        if ($this->_selfTransformers) {
            foreach ($this->_selfTransformers as $transformer) {
                try {
                    $originalFile = $transformer->transform($originalFile, true);

                } catch (Exception $e) {
                    $error = $e->getMessage();
                    break;
                }
            }

            $originalFile->reset();
        }

        // Create transformed files based off original
        if ($this->_transformers && !$error) {
            foreach ($this->_transformers as $transformer) {
                try {
                    $transformedFiles[] = $transformer->transform($originalFile, false);

                } catch (Exception $e) {
                    $error = $e->getMessage();
                    break;
                }
            }
        }

        $this->_file = $originalFile;
        $this->_files = $transformedFiles;

        // Throw error and rollback
        if ($error) {
            $this->rollback();

            throw new TransformationException($error);
        }

        return true;
    }

    /**
     * Transport the file using the Transporter object.
     * An array of configuration can be passed to override the transporter configuration.
     * If the configuration is numerically indexed, individual file overrides can be set.
     *
     * @param array $config
     * @return array
     * @throws \Transit\Exception\IoException
     * @throws \Transit\Exception\TransportationException
     * @throws \InvalidArgumentException
     */
    public function transport(array $config = array()) {
        if (!$this->_transporter) {
            throw new InvalidArgumentException('No Transporter has been defined');
        }

        $localFiles = $this->getAllFiles();
        $transportedFiles = array();
        $error = null;

        if (!$localFiles) {
            throw new IoException('No files to transport');
        }

        foreach ($localFiles as $i => $file) {
            try {
                $tempConfig = $config;

                if (isset($tempConfig[$i])) {
                    $tempConfig = array_merge($tempConfig, $tempConfig[$i]);
                }

                $transportedFiles[] = $this->getTransporter()->transport($file, $tempConfig);

            } catch (Exception $e) {
                $error = $e->getMessage();
                break;
            }
        }

        // Throw error and rollback
        if ($error) {
            $this->rollback();

            if ($transportedFiles) {
                foreach ($transportedFiles as $path) {
                    $this->getTransporter()->delete($path);
                }
            }

            throw new TransportationException($error);
        }

        return $transportedFiles;
    }

    /**
     * Upload the file to the target directory.
     *
     * @param bool $overwrite
     * @return bool
     * @throws \Transit\Exception\ValidationException
     */
    public function upload($overwrite = false) {
        $data = $this->_data;

        if (empty($data['tmp_name'])) {
            throw new ValidationException('Invalid file detected for upload');
        }

        // Check upload errors
        if ($data['error'] > 0 || !$this->_isUploadedFile($data['tmp_name'])) {
            switch ($data['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = 'File exceeds the maximum file size';
                break;
                case UPLOAD_ERR_PARTIAL:
                    $error = 'File was only partially uploaded';
                break;
                case UPLOAD_ERR_NO_FILE:
                    $error = 'No file was found for upload';
                break;
                default:
                    $error = 'File failed to upload';
                break;
            }

            throw new ValidationException($error);
        }

        // Validate rules
        if ($validator = $this->getValidator()) {
            $validator
                ->setFile(new File($data))
                ->validate();
        }

        // Upload the file
        $target = $this->findDestination($data['name'], $overwrite);

        if ($this->_moveUploadedFile($data['tmp_name'], $target)) {
            $data['name'] = basename($target);
            $data['tmp_name'] = $target;

            $this->_file = new File($data);

            return true;
        }

        throw new ValidationException('An unknown error has occurred');
    }

    /**
     * Return true if the file was uploaded via HTTP and is a valid file.
     *
     * @param string $tempFile
     * @return bool
     */
    protected function _isUploadedFile($tempFile) {
        return (is_uploaded_file($tempFile) || is_file($tempFile));
    }

    /**
     * Attempt to copy/move the uploaded file to the target destination.
     *
     * @param string $tempFile
     * @param string $target
     * @return bool
     */
    protected function _moveUploadedFile($tempFile, $target) {
        return (move_uploaded_file($tempFile, $target) || copy($tempFile, $target));
    }

}