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

class CropTransformerTest extends TestCase {

    /**
     * Test that cropping retains aspect ratio depending on the large side.
     */
    public function testTransform() {
        $object = new CropTransformer(array('width' => 100, 'height' => 235));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(100, $file->width());
        $this->assertEquals(235, $file->height());
    }

    /**
     * Test that an exception is thrown if no settings are defined.
     */
    public function testTransformException() {
        $object = new CropTransformer();

        try {
            $object->transform(new File($this->baseFile));
            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test that excluding the height will automatically generate a correct ratio.
     */
    public function testTransformHeightRatio() {
        $object = new CropTransformer(array('width' => 100));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(100, $file->width());
        $this->assertEquals(155, $file->height());

        $object = new CropTransformer(array('width' => 75, 'location' => CropTransformer::LEFT));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(75, $file->width());
        $this->assertEquals(116, $file->height());

        $object = new CropTransformer(array('width' => 137, 'location' => CropTransformer::BOTTOM));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(137, $file->width());
        $this->assertEquals(212, $file->height());
    }

    /**
     * Test that excluding the width will automatically generate a correct ratio.
     */
    public function testTransformWidthRatio() {
        $object = new CropTransformer(array('height' => 225));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(146, $file->width());
        $this->assertEquals(225, $file->height());

        $object = new CropTransformer(array('height' => 145, 'location' => CropTransformer::RIGHT));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(94, $file->width());
        $this->assertEquals(145, $file->height());

        $object = new CropTransformer(array('height' => 333, 'location' => CropTransformer::TOP));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(215, $file->width());
        $this->assertEquals(333, $file->height());
    }

}
