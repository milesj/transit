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

class ResizeTransformerTest extends TestCase {

    /**
     * Test that expand enables or disables resizing larger than original images.
     */
    public function testTransformExpand() {
        $object = new ResizeTransformer(array('width' => 666, 'aspect' => false, 'expand' => false));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(485, $file->width());
        $this->assertEquals(750, $file->height());

        $object = new ResizeTransformer(array('width' => 666, 'aspect' => false, 'expand' => true));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(666, $file->width());
        $this->assertEquals(1030, $file->height());
    }

    /**
     * Test that resizing with no aspect ratio is resized to exact dimensions.
     */
    public function testTransformNoAspectRatio() {
        $object = new ResizeTransformer(array('width' => 100, 'height' => 100, 'aspect' => false));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(100, $file->width());
        $this->assertEquals(100, $file->height());

        $object = new ResizeTransformer(array('width' => 333, 'height' => 117, 'aspect' => false));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(333, $file->width());
        $this->assertEquals(117, $file->height());
    }

    /**
     * Test that an exception is thrown if no settings are defined.
     */
    public function testTransformException() {
        $object = new ResizeTransformer();

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
        $object = new ResizeTransformer(array('width' => 100));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(100, $file->width());
        $this->assertEquals(155, $file->height());

        $object = new ResizeTransformer(array('width' => 75));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(75, $file->width());
        $this->assertEquals(116, $file->height());

        $object = new ResizeTransformer(array('width' => 137));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(137, $file->width());
        $this->assertEquals(212, $file->height());
    }

    /**
     * Test that excluding the width will automatically generate a correct ratio.
     */
    public function testTransformWidthRatio() {
        $object = new ResizeTransformer(array('height' => 225));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(146, $file->width());
        $this->assertEquals(225, $file->height());

        $object = new ResizeTransformer(array('height' => 145));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(94, $file->width());
        $this->assertEquals(145, $file->height());

        $object = new ResizeTransformer(array('height' => 333));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(215, $file->width());
        $this->assertEquals(332, $file->height());
    }

}
