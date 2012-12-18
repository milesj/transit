<?php

namespace mjohnson\transit\transformers;

use mjohnson\transit\File;
use \Exception;

abstract class TransformerAbstract implements Transformer {

	/**
	 * File object.
	 *
	 * @access protected
	 * @var \mjohnson\transit\File
	 */
	protected $_file;

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Base image width.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_width = 0;

	/**
	 * Base image height.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_height = 0;

	/**
	 * Store the file and gather image dimensions.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @param array $config
	 */
	public function __construct(File $file, array $config = array()) {
		$dims = $file->dimensions();

		if (!$dims) {
			throw new Exception(sprintf('%s is not a valid image.', $file->path()));
		}

		$this->_file = $file;
		$this->_width = $dims['width'];
		$this->_height = $dims['height'];
		$this->_config = $config + $this->_config;
	}

	/**
	 * Return the file object.
	 *
	 * @access public
	 * @return \mjohnson\transit\File
	 */
	public function getFile() {
		return $this->_file;
	}

	/**
	 * Transform the image using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return string
	 */
	public function process(array $options) {
		$options = $options + array(
			'dest_x' => 0,
			'dest_y' => 0,
			'dest_w' => null,
			'dest_h' => null,
			'source_x' => 0,
			'source_y' => 0,
			'source_w' => $this->_width,
			'source_h' => $this->_height,
			'quality' => 100
		);

		$file = $this->getFile();
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
				return null;
			break;
		}

		$targetImage = imagecreatetruecolor($options['dest_w'], $options['dest_h']);

		// If gif/png allow transparencies
		if ($mimeType == 'image/gif' || $mimeType == 'image/png') {
			imagealphablending($targetImage, false);
			imagesavealpha($targetImage, true);
			imagefilledrectangle($targetImage, 0, 0, $options['dest_w'], $options['dest_h'], imagecolorallocatealpha($targetImage, 255, 255, 255, 127));
		}

		// Lets take our source and apply it to the temporary file and resize
		imagecopyresampled($targetImage, $sourceImage, $options['dest_x'], $options['dest_y'], $options['source_x'], $options['source_y'], $options['dest_w'], $options['dest_h'], $options['source_w'], $options['source_h']);

		// Now write the transformed image to the server
		$targetPath = sprintf('%s%s.%s', $file->dir(), md5($file->name() . time()), $file->ext());

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
			default:
				imagedestroy($sourceImage);
				imagedestroy($targetImage);
				return null;
			break;
		}

		// Clear memory
		imagedestroy($sourceImage);
		imagedestroy($targetImage);

		return $targetPath;
	}

}