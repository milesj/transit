<?php

namespace Transit\Test;

class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * Class instance.
	 *
	 * @access protected
	 * @var object
	 */
	protected $object;

	/**
	 * Mock $_FILES data.
	 *
	 * @access protected
	 * @var array
	 */
	protected $data = array();

	/**
	 * Base file for testing.
	 *
	 * @access protected
	 * @var string
	 */
	protected $baseFile;

	/**
	 * Temporary file for uploading.
	 *
	 * @access protected
	 * @var string
	 */
	protected $tempFile;

	/**
	 * Create temporary test files to mimic file uploading.
	 */
	protected function setUp() {
		$baseFile = TEST_DIR . '/tmp/scott-pilgrim.jpg';
		$tempFile = TEST_DIR . '/tmp/test.jpg';

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
