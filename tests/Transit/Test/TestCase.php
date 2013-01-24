<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Test;

class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * Class instance.
	 *
	 * @var object
	 */
	protected $object;

	/**
	 * Mock $_FILES data.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Base file for testing.
	 *
	 * @var string
	 */
	protected $baseFile;

	/**
	 * Temporary file for uploading.
	 *
	 * @var string
	 */
	protected $tempFile;

	/**
	 * Create temporary test files to mimic file uploading.
	 */
	protected function setUp() {
		$baseFile = TEMP_DIR . '/scott-pilgrim.jpg';
		$tempFile = TEMP_DIR . '/test.jpg';

		copy($baseFile, $tempFile);

		$this->baseFile = $baseFile;
		$this->tempFile = $tempFile;
		$this->data = array(
			'name' => basename($baseFile),
			'type' => 'image/jpeg',
			'tmp_name' => $tempFile,
			'error' => 0,
			'size' => filesize($baseFile)
		);
	}

	/**
	 * Delete the temporary file.
	 */
	protected function tearDown() {
		@unlink($this->tempFile);
	}

}
