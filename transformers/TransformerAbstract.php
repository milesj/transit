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
	 */
	public function __construct(File $file) {
		$dims = $file->dimensions();

		if (!$dims) {
			throw new Exception(sprintf('%s is not a valid image.', $file->path()));
		}

		$this->_file = $file;
		$this->_width = $dims['width'];
		$this->_height = $dims['height'];
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
	 * @return boolean
	 */
	public function transform(array $options) {
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

		$sourcePath = $this->getFile()->path();
		$mimeType = $this->getFile()->type();

		// Create an image to work with
		switch ($mimeType) {
			case 'image/gif':
				$source = imagecreatefromgif($sourcePath);
			break;
			case 'image/png':
				$source = imagecreatefrompng($sourcePath);
			break;
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/pjpeg':
				$source = imagecreatefromjpeg($sourcePath);
			break;
			default:
				return false;
			break;
		}

		$target = imagecreatetruecolor($options['dest_w'], $options['dest_h']);

		// If gif/png allow transparencies
		if ($mimeType == 'image/gif' || $mimeType == 'image/png') {
			imagealphablending($target, false);
			imagesavealpha($target, true);
			imagefilledrectangle($target, 0, 0, $options['dest_w'], $options['dest_h'], imagecolorallocatealpha($target, 255, 255, 255, 127));
		}

		// Lets take our source and apply it to the temporary file and resize
		imagecopyresampled($target, $source, $options['dest_x'], $options['dest_y'], $options['source_x'], $options['source_y'], $options['dest_w'], $options['dest_h'], $options['source_w'], $options['source_h']);

		// Now write the transformed image to the server
		switch ($mimeType) {
			case 'image/gif':
				imagegif($target, $options['target']);
			break;
			case 'image/png':
				imagepng($target, $options['target']);
			break;
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg($target, $options['target'], $options['quality']);
			break;
			default:
				imagedestroy($source);
				imagedestroy($target);
				return false;
			break;
		}

		// Clear memory
		imagedestroy($source);
		imagedestroy($target);

		return true;
	}

}