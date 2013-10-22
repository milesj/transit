<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use Transit\Test\TestCase;
use \Exception;

class FlipTransformerTest extends TestCase {

    /**
     * Test that flipping directions work correctly.
     */
    public function testTransform() {
        $object = new FlipTransformer(array('direction' => FlipTransformer::VERTICAL));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(485, $file->width());
        $this->assertEquals(750, $file->height());

        $object = new FlipTransformer(array('direction' => FlipTransformer::HORIZONTAL));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(485, $file->width());
        $this->assertEquals(750, $file->height());

        $object = new FlipTransformer(array('direction' => FlipTransformer::BOTH));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(485, $file->width());
        $this->assertEquals(750, $file->height());
    }

    /**
     * Test that an exception is thrown if the wrong settings are defined.
     */
    public function testTransformException() {
        $object = new FlipTransformer(array('direction' => 'foobar'));

        try {
            $object->transform(new File($this->baseFile));
            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

}
