<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers\image;

use mjohnson\transit\File;
use mjohnson\transit\transformers\AbstractTransformer;
use \Exception;

/**
 * Provides shared functionality for transformers.
 *
 * @package	mjohnson.transit.transformers.image
 */
abstract class AbstractImageTransformer extends AbstractTransformer {

	/**
	 * Set the File object.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @return \mjohnson\transit\transformers\Transformer
	 * @throws \Exception
	 */
	public function setFile(File $file) {
		if (!extension_loaded('gd')) {
			throw new Exception('GD image library is not installed.');
		}

		if (!$file->isImage()) {
			throw new Exception(sprintf('%s is not a valid image.', $file->basename()));
		}

		return parent::setFile($file);
	}

	/**
	 * Transform the image using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return \mjohnson\transit\File
	 * @throws \Exception
	 */
	public function process(array $options) {
		$file = $this->getFile();
		$options = $options + array(
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
			'target' => ''
		);

		$sourcePath = $file->path();
		$mimeType = $file->type();

		// Create an image to work with
		switch ($mimeType) {
			case 'image/gif':
				$sourceImage = imagecreatefromgif($sourcePath);
			break;
			case 'image/png':
				$sourceImage = imagecreatefrompng($sourcePath);
			break;
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/pjpeg':
				$sourceImage = imagecreatefromjpeg($sourcePath);
			break;
			default:
				throw new Exception(sprintf('%s can not be transformed.', $mimeType));
			break;
		}

		$targetImage = imagecreatetruecolor($options['dest_w'], $options['dest_h']);

		// If gif/png allow transparencies
		if ($mimeType === 'image/gif' || $mimeType === 'image/png') {
			imagealphablending($targetImage, false);
			imagesavealpha($targetImage, true);
			imagefilledrectangle($targetImage, 0, 0, $options['dest_w'], $options['dest_h'], imagecolorallocatealpha($targetImage, 255, 255, 255, 127));
		}

		// Lets take our source and apply it to the temporary file and resize
		imagecopyresampled($targetImage, $sourceImage, $options['dest_x'], $options['dest_y'], $options['source_x'], $options['source_y'], $options['dest_w'], $options['dest_h'], $options['source_w'], $options['source_h']);

		// Now write the transformed image to the server
		if ($options['overwrite']) {
			$options['target'] = $file->name();

		} else if (!$options['target']) {
			$options['target'] = sprintf('%s-%sx%s', $file->name(), round($options['dest_w']), round($options['dest_h']));
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