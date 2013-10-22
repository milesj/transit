<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Validator;

use Transit\File;
use Transit\Test\TestCase;
use \Exception;

class ValidatorTest extends TestCase {

    /**
     * Initialize the validator.
     */
    protected function setUp() {
        parent::setUp();

        $this->object = new ImageValidator();
        $this->object->setFile(new File($this->baseFile));
    }

    /**
     * Test that the file size is below the max.
     */
    public function testSize() {
        $this->assertTrue($this->object->size(130000));
        $this->assertFalse($this->object->size(100000));
    }

    /**
     * Test that the file extension is in the list.
     */
    public function testExt() {
        $this->assertTrue($this->object->ext('jpg'));
        $this->assertFalse($this->object->ext(array('gif', 'png')));
    }

    /**
     * Test that the file mime type is in the list.
     */
    public function testType() {
        $this->assertTrue($this->object->type('image'));
        $this->assertFalse($this->object->type('archive'));
    }

    /**
     * Test that the file mime type is in the list.
     */
    public function testMimeType() {
        $this->assertTrue($this->object->mimeType('image/jpeg'));
        $this->assertFalse($this->object->mimeType(array('image/png', 'image/gif')));
    }

    /**
     * Test that addRule() sets rules and validate() validates them.
     */
    public function testAddRuleAndValidate() {
        $this->object->addRule('size', 'Size too large', 130000);

        try {
            $this->object->validate();

            $this->assertTrue(true);

        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        $this->object->addRule('ext', 'Invalid extension', array(array('png', 'gif')));

        try {
            $this->object->validate();

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test that validate() throws exceptions for invalid methods.
     */
    public function testValidateException() {
        $this->object->addRule('foobar', 'Invalid method');

        try {
            $this->object->validate();

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

}
