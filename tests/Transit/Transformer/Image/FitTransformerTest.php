<?php
/**
 * Based on the code written by Miles Johnson
 *
 * @copyright	Copyright 2013, Serge Rodovnichenko - http://www.handmadesite.net
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit\Transformer\Image;

use Transit\File;
use Transit\Test\TestCase;
use \Exception;

class FitTransformerTest extends TestCase {

	/**
	 * Test that expand enables or disables resizing larger than original images.
	 */
	public function testTransformExpand() {
		$object = new FitTransformer(array('maxWidth' => 666, 'maxHeight'=>999,'aspect' => false, 'expand' => false));
		$file = $object->transform(new File($this->baseFile));

		$this->assertEquals(485, $file->width());
		$this->assertEquals(750, $file->height());

		$object = new FitTransformer(array('maxWidth' => 900, 'maxHeight'=>1000, 'aspect' => false, 'expand' => true));
		$file = $object->transform(new File($this->baseFile));

		$this->assertEquals(646, $file->width());
		$this->assertEquals(1000, $file->height());
	}

	/**
	 * Test that an exception is thrown if no settings are defined.
	 */
	public function testTransformException() {
		$object = new FitTransformer();

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
	public function testTransform() {
		$object = new FitTransformer(array('maxWidth' => 100, 'maxHeight' => 100));
		$file = $object->transform(new File($this->baseFile));

		$this->assertEquals(64, $file->width());
		$this->assertEquals(100, $file->height());
	}

}