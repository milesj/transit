<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transporter\Aws;

use Transit\File;
use Transit\Test\TestCase;
use \Exception;

class GlacierTransporterTest extends TestCase {

    /**
     * Test that uploading a file to Glacier returns an archive ID and deleting the file via the ID works.
     */
    public function testTransportAndDelete() {
        $this->checkGlacier();

        $object = new GlacierTransporter(AWS_ACCESS, AWS_SECRET, array(
            'vault' => GLACIER_VAULT,
            'region' => GLACIER_REGION
        ));

        try {
            copy($this->baseFile, $this->tempFile);

            if ($response = $object->transport(new File($this->tempFile))) {
                $this->assertNotEmpty($response);
            } else {
                $this->assertTrue(false);
            }
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        if (isset($response)) {
            $this->assertTrue($object->delete($response));
        }
    }

    /**
     * Test that exceptions are thrown if settings are missing.
     */
    public function testExceptionHandling() {
        try {
            new GlacierTransporter(AWS_ACCESS, AWS_SECRET, array(
                'vault' => GLACIER_VAULT
            ));

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new GlacierTransporter(AWS_ACCESS, AWS_SECRET, array(
                'region' => GLACIER_REGION
            ));

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

}
