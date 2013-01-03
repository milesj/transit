<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\Test\TestCase;

class FileTest extends TestCase {

	/**
	 * Store the file object.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new File($this->tempFile);
	}

	/**
	 * Test that basename() returns file name with extension.
	 */
	public function testBasename() {
		$this->assertEquals('test.jpg', $this->object->basename());
	}

	/**
	 * Test that delete() deletes the file.
	 */
	public function testDelete() {
		$this->assertTrue(file_exists($this->tempFile));

		$this->object->delete();

		$this->assertFalse(file_exists($this->tempFile));
	}

	/**
	 * Test that dimensions() returns width and height of an image, null if not.
	 */
	public function testDimensions() {
		$this->assertEquals(array(
			'width' => 485,
			'height' => 750
		), $this->object->dimensions());
	}

	/**
	 * Test that dir() returns the parent folder.
	 */
	public function testDir() {
		$this->assertEquals(TEMP_DIR . '/', $this->object->dir());
	}

	/**
	 * Test that ext() returns the extension.
	 */
	public function testExt() {
		$this->assertEquals('jpg', $this->object->ext());
	}

	/**
	 * Test that height() returns the image height.
	 */
	public function testHeight() {
		$this->assertEquals(750, $this->object->height());
	}

	/**
	 * Test that isApplication() returns true if the mime type is application.
	 */
	public function testIsApplication() {
		$this->assertFalse($this->object->isApplication());
	}

	/**
	 * Test that isAudio() returns true if the mime type is audio.
	 */
	public function testIsAudio() {
		$this->assertFalse($this->object->isAudio());
	}

	/**
	 * Test that isImage() returns true if the mime type is an image.
	 */
	public function testIsImage() {
		$this->assertTrue($this->object->isImage());
	}

	/**
	 * Test that isText() returns true if the mime type is text.
	 */
	public function testIsText() {
		$this->assertFalse($this->object->isText());
	}

	/**
	 * Test that isVideo() returns true if the mime type is video.
	 */
	public function testIsVideo() {
		$this->assertFalse($this->object->isVideo());
	}

	/**
	 * Test that isSubType() returns true if the mime type is a sub-type.
	 */
	public function testIsSubType() {
		$this->assertFalse($this->object->isSubType('archive'));
	}

	/**
	 * Test that move() moves the file to another directory.
	 */
	public function testMove() {
		$newPath = TEST_DIR . '/test.jpg';

		$this->assertTrue(file_exists($this->tempFile));
		$this->assertFalse(file_exists($newPath));

		$this->object->move(TEST_DIR);

		$this->assertFalse(file_exists($this->tempFile));
		$this->assertTrue(file_exists($newPath));

		$this->assertTrue($this->object->move(TEST_DIR));

		$this->object->delete();
	}

	/**
	 * Test that move() doesn't overwrite files but appends an incremented number.
	 */
	public function testMoveNoOverwrite() {
		$testPath = TEST_DIR . '/test.jpg';
		$movePath = TEMP_DIR . '/test-1.jpg';

		copy($this->baseFile, $testPath);

		$this->assertFalse(file_exists($movePath));

		$file = new File($testPath);
		$file->move(TEMP_DIR);

		$this->assertTrue(file_exists($movePath));

		$file->delete();
	}

	/**
	 * Test that name() returns the file name without extension.
	 */
	public function testName() {
		$this->assertEquals('test', $this->object->name());
	}

	/**
	 * Test that path() returns the absolute file path.
	 */
	public function testPath() {
		$this->assertEquals($this->tempFile, $this->object->path());
	}

	/**
	 * Test that rename() renames the file and includes append and prepend text.
	 */
	public function testRename() {
		$this->assertTrue(file_exists($this->tempFile));

		// Callback
		$time = time();

		$this->object->rename(function($name) use ($time) {
			return $time;
		});

		$this->assertFalse(file_exists($this->tempFile));
		$this->assertEquals($time, $this->object->name());

		// String with append, prepend
		$this->object->rename('callback', 'app', 'pre');

		$this->assertFalse(file_exists($this->tempFile));
		$this->assertEquals('precallbackapp', $this->object->name());

		// Reset name
		$this->object->rename('test');

		$this->assertTrue(file_exists($this->tempFile));
		$this->assertEquals('test', $this->object->name());
	}

	/**
	 * Test that size() returns the file size.
	 */
	public function testSize() {
		$this->assertEquals(126869, $this->object->size());
	}

	/**
	 * Test that type() returns the mime type.
	 */
	public function testType() {
		$this->assertEquals('image/jpeg', $this->object->type());
	}

	/**
	 * Test that width() returns the image width.
	 */
	public function testWidth() {
		$this->assertEquals(485, $this->object->width());
	}

	/**
	 * Test that toArray() returns all the meta data as an array.
	 */
	public function testToArray() {
		$this->assertEquals(array(
			'basename' => 'test.jpg',
			'dir' => dirname($this->tempFile) . '/',
			'ext' => 'jpg',
			'name' => 'test',
			'path' => $this->tempFile,
			'size' => 126869,
			'type' => 'image/jpeg',
			'height' => 750,
			'width' => 485
		), $this->object->toArray());
	}

	/**
	 * Test that toString() returns the file path.
	 */
	public function testToString() {
		$this->assertEquals($this->tempFile, $this->object->toString());
		$this->assertEquals($this->tempFile, (string) $this->object);
	}

}
