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

class ScaleTransformerTest extends TestCase {

    /**
     * Test that scaling creates smaller or larger images while keeping ratio.
     */
    public function testTransform() {
        $object = new ScaleTransformer(array('percent' => .3));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(146, $file->width());
        $this->assertEquals(225, $file->height());

        $object = new ScaleTransformer(array('percent' => .7));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(340, $file->width());
        $this->assertEquals(525, $file->height());

        $object = new ScaleTransformer(array('percent' => 1.4));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(679, $file->width());
        $this->assertEquals(1050, $file->height());
    }

    /**
     * Test that an exception is thrown if the wrong settings are defined.
     */
    public function testTransformException() {
        $object = new ScaleTransformer(array('percent' => null));

        try {
            $object->transform(new File($this->baseFile));
            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

}
