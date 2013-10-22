<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\Test\TestCase;
use \Exception;

class MimeTypeTest extends TestCase {

    /**
     * Test that addSubType() creates sub-types.
     */
    public function testAddSubType() {
        MimeType::addSubType('img', array('gif', 'png', 'jpg', 'jpeg'));

        $this->assertTrue(MimeType::isSubType('img', 'image/gif'));
    }

    /**
     * Test that getExtFromType() returns a list of ext from the mime type.
     */
    public function testGetExtFromType() {
        $this->assertEquals(array('jpe', 'jpeg', 'jpg'), MimeType::getExtFromType('image/jpeg'));
        $this->assertEquals('jpg', MimeType::getExtFromType('image/jpeg', true));
        $this->assertEquals(array('png'), MimeType::getExtFromType('image/png'));
        $this->assertEquals(array(), MimeType::getExtFromType('foo/bar'));
        $this->assertEquals('txt', MimeType::getExtFromType('text/plain', true));
    }

    /**
     * Test that getTypeFromExt() returns a mime type from the ext.
     */
    public function testGetTypeFromExt() {
        $this->assertEquals('image/jpeg', MimeType::getTypeFromExt('jpg'));
        $this->assertEquals('image/png', MimeType::getTypeFromExt('png'));
        $this->assertEquals('application/x-7z-compressed', MimeType::getTypeFromExt('7z'));

        try {
            MimeType::getTypeFromExt('foobar');

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test that getList() methods return the correct amount of mime types.
     */
    public function testGetList() {
        $this->assertEquals(662, count(MimeType::getApplicationList()));
        $this->assertEquals(42, count(MimeType::getAudioList()));
        $this->assertEquals(54, count(MimeType::getImageList()));
        $this->assertEquals(76, count(MimeType::getTextList()));
        $this->assertEquals(51, count(MimeType::getVideoList()));
    }

    /**
     * Test that getSubTypeList() returns a list of mime types.
     */
    public function testGetSubTypeList() {
        $this->assertEquals(12, count(MimeType::getSubTypeList('archive')));
        $this->assertEquals(14, count(MimeType::getSubTypeList('spreadsheet')));
        $this->assertEquals(9, count(MimeType::getSubTypeList('document')));

        try {
            MimeType::getSubTypeList('foobar');

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test that is() methods return a bool dependent on the mime type.
     */
    public function testIs() {
        $this->assertTrue(MimeType::isApplication('application/x-7z-compressed'));
        $this->assertFalse(MimeType::isApplication('image/png'));

        $this->assertTrue(MimeType::isAudio('audio/midi'));
        $this->assertFalse(MimeType::isAudio('text/xml'));

        $this->assertTrue(MimeType::isImage('image/gif'));
        $this->assertFalse(MimeType::isImage('text/plain'));

        $this->assertTrue(MimeType::isText('text/yaml'));
        $this->assertFalse(MimeType::isText('video/mp4'));

        $this->assertTrue(MimeType::isVideo('video/mp4'));
        $this->assertFalse(MimeType::isVideo('application/x-7z-compressed'));

        $this->assertTrue(MimeType::isSubType('archive', 'application/x-7z-compressed'));
        $this->assertFalse(MimeType::isSubType('archive', 'video/mp4'));
    }

}
