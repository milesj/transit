<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Validator;

use Transit\File;
use Transit\Test\TestCase;

class ImageValidatorTest extends TestCase {

    /**
     * Initialize the validator.
     */
    protected function setUp() {
        parent::setUp();

        $this->object = new ImageValidator();
        $this->object->setFile(new File($this->baseFile));
    }

    /**
     * Test that the file height is exact.
     */
    public function testHeight() {
        $this->assertTrue($this->object->height(750));
        $this->assertFalse($this->object->height(550));
    }

    /**
     * Test that the file width is exact.
     */
    public function testWidth() {
        $this->assertTrue($this->object->width(485));
        $this->assertFalse($this->object->width(335));
    }

    /**
     * Test that the file height is greater than the minimum.
     */
    public function testMinHeight() {
        $this->assertTrue($this->object->minHeight(400));
        $this->assertFalse($this->object->minHeight(900));
    }

    /**
     * Test that the file width is greater than the minimum.
     */
    public function testMinWidth() {
        $this->assertTrue($this->object->minWidth(355));
        $this->assertFalse($this->object->minWidth(500));
    }

    /**
     * Test that the file height is less than the maximum.
     */
    public function testMaxHeight() {
        $this->assertTrue($this->object->maxHeight(900));
        $this->assertFalse($this->object->maxHeight(555));
    }

    /**
     * Test that the file width is less than the maximum.
     */
    public function testMaxWidth() {
        $this->assertTrue($this->object->maxWidth(550));
        $this->assertFalse($this->object->maxWidth(275));
    }

}
