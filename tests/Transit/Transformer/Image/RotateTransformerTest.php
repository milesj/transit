<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use Transit\Test\TestCase;

class RotateTransformerTest extends TestCase {

    /**
     * Test that flipping directions work correctly.
     */
    public function testTransform() {
        $object = new RotateTransformer(array('degrees' => 90));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(750, $file->width());
        $this->assertEquals(485, $file->height());

        $object = new RotateTransformer(array('degrees' => 180));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(485, $file->width());
        $this->assertEquals(750, $file->height());

        $object = new RotateTransformer(array('degrees' => -90));
        $file = $object->transform(new File($this->baseFile));

        $this->assertEquals(750, $file->width());
        $this->assertEquals(485, $file->height());
    }

}
