<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use Transit\Transformer\AbstractTransformer;
use \DomainException;
use \RuntimeException;

/**
 * Provides shared functionality for transformers.
 *
 * @package Transit\Transformer\Image
 */
abstract class AbstractImageTransformer extends AbstractTransformer {

    /**
     * Store configuration.
     *
     * @param array $config
     * @throws \RuntimeException
     */
    public function __construct(array $config = array()) {
        if (!extension_loaded('gd')) {
            throw new RuntimeException('GD image library is not installed');
        }

        return parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \DomainException
     */
    protected function _process(File $file, array $options) {
        if (!$file->isImage()) {
            throw new DomainException(sprintf('%s is not a valid image', $file->basename()));
        }

        $sourcePath = $file->path();
        $mimeType = $file->type();

        // Create an image to work with
        switch ($mimeType) {
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($sourcePath);
            break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($sourcePath);
            break;
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $sourceImage = @imagecreatefromjpeg($sourcePath);
            break;
            default:
                $sourceImage = false;
            break;
        }

        if (!$sourceImage) {
            throw new DomainException(sprintf('%s can not be transformed', $mimeType));
        }

        // Gather options
        $options = $options + array(
            'width' => null,
            'height' => null,
            'dest_x' => 0,
            'dest_y' => 0,
            'dest_w' => null,
            'dest_h' => null,
            'source_x' => 0,
            'source_y' => 0,
            'source_w' => $file->width(),
            'source_h' => $file->height(),
            'quality' => 100,
            'overwrite' => false,
            'target' => '',
            'preCallback' => '',
            'postCallback' => ''
        );

        $options = array_map(function($value) {
            if (is_numeric($value)) {
                $value = round($value);
            }

            return $value;
        }, $options);

        $width = $options['width'] ?: $options['dest_w'];
        $height = $options['height'] ?: $options['dest_h'];
        $targetImage = imagecreatetruecolor($width, $height);

        // If gif/png allow transparencies
        if ($mimeType === 'image/gif' || $mimeType === 'image/png') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            imagefill($targetImage, 0, 0, imagecolorallocatealpha($targetImage, 255, 255, 255, 127));
        }

        // Trigger a callback to prepare the image
        if (is_callable($options['preCallback'])) {
            $targetImage = call_user_func_array($options['preCallback'], array($targetImage, $options));
        }

        // Lets take our source and apply it to the temporary file and resize
        imagecopyresampled($targetImage, $sourceImage, $options['dest_x'], $options['dest_y'], $options['source_x'], $options['source_y'], $options['dest_w'], $options['dest_h'], $options['source_w'], $options['source_h']);

        // Trigger a callback to modify the image
        if (is_callable($options['postCallback'])) {
            $targetImage = call_user_func_array($options['postCallback'], array($targetImage, $options));
        }

        // Now write the transformed image to the server
        if ($options['overwrite']) {
            $options['target'] = $file->name();

        } else if (!$options['target']) {
            $class = explode('\\', get_class($this));
            $class = str_replace('transformer', '', strtolower(end($class)));

            $options['target'] = sprintf('%s-%s-%sx%s-%s', $file->name(), $class, $width, $height, uniqid());
        }

        $targetPath = sprintf('%s%s.%s', $file->dir(), $options['target'], $file->ext());

        switch ($mimeType) {
            case 'image/gif':
                imagegif($targetImage, $targetPath);
            break;
            case 'image/png':
                imagepng($targetImage, $targetPath);
            break;
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($targetImage, $targetPath, $options['quality']);
            break;
        }

        // Clear memory
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return new File($targetPath);
    }

}
