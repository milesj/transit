<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use Transit\Test\TestCase;

class ExifTransformerTest extends TestCase {

    /**
     * Test that exif orientation is fixed and exif data is stripped.
     */
    public function testLandscape() {
        $exif = array(
            'make' => '',
            'model' => '',
            'exposure' => '',
            'orientation' => '',
            'fnumber' => '',
            'date' => '',
            'iso' => '',
            'focal' => ''
        );

        // 1
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_1.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 2
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_2.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 3
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_3.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 4
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_4.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 5
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_5.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 6
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_6.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 7
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_7.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 8
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Landscape_8.jpg'));

        $this->assertEquals(600, $file->width());
        $this->assertEquals(450, $file->height());
        $this->assertEquals($exif, $file->exif());
    }

    /**
     * Test that exif orientation is fixed and exif data is stripped.
     */
    public function testPortrait() {
        $exif = array(
            'make' => '',
            'model' => '',
            'exposure' => '',
            'orientation' => '',
            'fnumber' => '',
            'date' => '',
            'iso' => '',
            'focal' => ''
        );

        // 1
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_1.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 2
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_2.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 3
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_3.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 4
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_4.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 5
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_5.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 6
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_6.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 7
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_7.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());

        // 8
        $or1 = new ExifTransformer();
        $file = $or1->transform(new File(TEMP_DIR . '/exif/Portrait_8.jpg'));

        $this->assertEquals(450, $file->width());
        $this->assertEquals(600, $file->height());
        $this->assertEquals($exif, $file->exif());
    }

}
