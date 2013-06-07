<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use Transit\Test\TestCase;

class ExifTransformerTest extends TestCase {

	/**
	 * Test that exif orientation is fixed and exif data is stripped.
	 */
	public function testTransform() {
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
		$file = $or1->transform(new File(TEMP_DIR . '/exif/1.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 2
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/2.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 3
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/3.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 4
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/4.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 5
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/5.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 6
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/6.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 7
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/7.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());

		// 8
		$or1 = new ExifTransformer();
		$file = $or1->transform(new File(TEMP_DIR . '/exif/8.jpg'));

		$this->assertEquals(600, $file->width());
		$this->assertEquals(450, $file->height());
		$this->assertEquals($exif, $file->exif());
	}

}
