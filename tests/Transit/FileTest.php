<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\Test\TestCase;
use \Exception;

class FileTest extends TestCase {

    /**
     * Store the file object.
     */
    protected function setUp() {
        parent::setUp();

        copy($this->baseFile, $this->tempFile);

        $this->object = new File($this->tempFile);
    }

    /**
     * Test object construction.
     */
    public function testConstruct() {
        try {
            $file = new File(array()); // missing tmp_name
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $file = new File(array('tmp_name' => 'some/path.jpg')); // invalid path
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test that basename() returns file name with extension.
     */
    public function testBasename() {
        $this->assertEquals('vertical-test.jpg', $this->object->basename());
    }

    /**
     * Test $_FILES data.
     */
    public function testData() {
        $this->assertEquals(null, $this->object->data('name'));

        // Test using an array
        $file = new File($this->data);
        $this->assertEquals('vertical-test.jpg', $file->data('name'));
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
     * Test that exif() returns exif data.
     */
    public function testExif() {
        $file = new File(TEMP_DIR . '/exif-data.jpg');

        $this->assertEquals(array(
            'make' => 'NIKON CORPORATION',
            'model' => 'NIKON 1 J1',
            'exposure' => '10/4000',
            'orientation' => 1,
            'fnumber' => '45/10',
            'date' => '2013:06:07 11:32:22',
            'iso' => 100,
            'focal' => '100/10'
        ), $file->exif());

        $file = new File(TEMP_DIR . '/magic-mime-verify.js');

        $this->assertEquals(array(
            'make' => '',
            'model' => '',
            'exposure' => '',
            'orientation' => '',
            'fnumber' => '',
            'date' => '',
            'iso' => '',
            'focal' => ''
        ), $file->exif());
    }

    /**
     * Test that ext() returns the extension.
     */
    public function testExt() {
        $this->assertEquals('jpg', $this->object->ext());

        // Test using an array
        $file = new File($this->data);
        $this->assertEquals('jpg', $file->ext());
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
        $newPath = TEST_DIR . '/vertical-test.jpg';

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
        $testPath = TEST_DIR . '/vertical-test.jpg';
        $movePath = TEMP_DIR . '/vertical-test-1.jpg';

        copy($this->baseFile, $testPath);
        $file = new File($testPath);

        $this->assertFalse(file_exists($movePath));
        $this->assertEquals('vertical-test', $file->name());

        $file->move(TEMP_DIR);

        $this->assertTrue(file_exists($movePath));
        $this->assertEquals('vertical-test-1', $file->name());

        $file->delete();
    }

    /**
     * Test that name() returns the file name without extension.
     */
    public function testName() {
        $this->assertEquals('vertical-test', $this->object->name());

        // Rename and test
        $this->object->rename('vertical-renamed');
        $this->assertEquals('vertical-renamed', $this->object->name());
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
        $this->object->rename('vertical-test');

        $this->assertTrue(file_exists($this->tempFile));
        $this->assertEquals('vertical-test', $this->object->name());
    }

    /**
     * Test that size() returns the file size.
     */
    public function testSize() {
        $this->assertEquals(126869, $this->object->size());
    }

    /**
     * Test supportsExif() returns a boolean.
     */
    public function testSupportsExif() {
        $file1 = new File(TEMP_DIR . '/exif-data.jpg');
        $file2 = new File(TEMP_DIR . '/magic-mime-verify.js');

        $this->assertTrue($file1->supportsExif());
        $this->assertFalse($file2->supportsExif());
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
            'basename' => 'vertical-test.jpg',
            'dir' => dirname($this->tempFile) . '/',
            'ext' => 'jpg',
            'name' => 'vertical-test',
            'path' => $this->tempFile,
            'size' => 126869,
            'type' => 'image/jpeg',
            'height' => 750,
            'width' => 485,
            'exif.make' => '',
            'exif.model' => '',
            'exif.exposure' => '',
            'exif.orientation' => '',
            'exif.fnumber' => '',
            'exif.date' => '',
            'exif.iso' => '',
            'exif.focal' => ''
        ), $this->object->toArray());
    }

    /**
     * Test that toString() returns the file path.
     */
    public function testToString() {
        $this->assertEquals($this->tempFile, $this->object->toString());
        $this->assertEquals($this->tempFile, (string) $this->object);
    }

    /**
     * Test for this bug: https://bugs.php.net/bug.php?id=53035
     */
    public function testTypeDetectionForMagicBugs() {
        $file = new File(TEMP_DIR . '/magic-mime-verify.js');

        // This will actually return text/plain because magic cant determine a text/javascript file
        // It can also return text/x-c in some weird corner cases
        // If either of these happen, fall back to the extension derived mimetype (or from $_FILES)
        $this->assertEquals('application/javascript', $file->type());
    }

}
