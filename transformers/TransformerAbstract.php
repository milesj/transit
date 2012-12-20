<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transformers;

use mjohnson\transit\File;
use \Exception;

/**
 * Provides shared functionality for transformers.
 *
 * @package	mjohnson.transit.transformers
 */
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

		if (!extension_loaded('gd')) {
			throw new Exception('GD image library is not installed.');
		}

		$this->_file = $file;
		$this->_width = $dims['width'];
		$this->_height = $dims['height'];
		$this->_config = $config + $this->_config;
	}

	/**
	 * Transform the image using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return \mjohnson\transit\File
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
			'quality' => 100,
			'overwrite' => false,
			'target' => ''
		);

		$file = $this->_file;
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