<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;

/**
 * Rotates and fixes an images orientation based on exif data.
 *
 * @package Transit\Transformer\Image
 */
class OrientationTransformer extends RotateTransformer {

	/**
	 * {@inheritdoc}
	 */
	public function transform(File $file, $self = false) {
		$exif = $file->exif();
		$self = true; // Always overwrite as we want to fix the original

		switch ($exif['orientation']) {
			case 3:
				$this->setConfig('degrees', 180);
			break;
			case 6:
				$this->setConfig('degrees', -90);
			break;
			case 8:
				$this->setConfig('degrees', 90);
			break;

			// Return current file if orientation is correct or no exif data
			default:
				return $file;
			break;
		}

		return $this->_process($file, array(
			'dest_w'	=> $file->width(),
			'dest_h'	=> $file->height(),
			'quality'	=> $this->getConfig('quality'),
			'overwrite'	=> $self,
			'callback'	=> array($this, 'rotate')
		));
	}

}