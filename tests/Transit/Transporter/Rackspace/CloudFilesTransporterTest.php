<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transporter\Rackspace;

use Transit\File;
use Transit\Test\TestCase;
use \Exception;

class CloudFilesTransporterTest extends TestCase {

    /**
     * Test that uploading a file to CloudFiles returns a URL and deleting the file via the URL works.
     */
    public function testTransportAndDelete() {
        $this->checkCloudFiles();

        $object = new CloudFilesTransporter(RACKSPACE_USER, RACKSPACE_KEY, array(
            'container' => CF_CONTAINER,
            'region' => CF_REGION
        ));

        copy($this->baseFile, $this->tempFile);
        $name = basename($this->tempFile);

        try {
            if ($response = $object->transport(new File($this->tempFile))) {
                $this->assertRegExp('/^http:\/\/(.*?).rackcdn.com\/' . $name . '$/', $response);
            } else {
                $this->assertTrue(false);
            }
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        if (isset($response)) {
            $this->assertTrue($object->delete($name));
        }
    }

    /**
     * Test uploading into a folder.
     */
    public function testTransportFolder() {
        $this->checkCloudFiles();

        $object = new CloudFilesTransporter(RACKSPACE_USER, RACKSPACE_KEY, array(
            'container' => CF_CONTAINER,
            'region' => CF_REGION
        ));

        copy($this->baseFile, $this->tempFile);
        $name = basename($this->tempFile);

        try {
            if ($response = $object->transport(new File($this->tempFile), array('folder' => '/folder/'))) {
                $this->assertRegExp('/^http:\/\/(.*?).rackcdn.com\/(.*?)' . $name . '$/', $response);
            } else {
                $this->assertTrue(false);
            }
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        if (isset($response)) {
            $this->assertTrue($object->delete('folder/' . $name));
        }
    }

    /**
     * Test returning the key instead of URL.
     */
    public function testReturnUrl() {
        $this->checkCloudFiles();

        $object = new CloudFilesTransporter(RACKSPACE_USER, RACKSPACE_KEY, array(
            'container' => CF_CONTAINER,
            'region' => CF_REGION
        ));

        copy($this->baseFile, $this->tempFile);
        $name = basename($this->tempFile);

        try {
            if ($response = $object->transport(new File($this->tempFile), array('returnUrl' => false))) {
                $this->assertEquals($name, $response);
            } else {
                $this->assertTrue(false);
            }
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        if (isset($response)) {
            $this->assertTrue($object->delete($name));
        }
    }

    /**
     * Test that exceptions are thrown if settings are missing.
     */
    public function testExceptionHandling() {
        try {
            new CloudFilesTransporter(RACKSPACE_USER, RACKSPACE_KEY, array(
                'container' => CF_CONTAINER,
            ));

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new CloudFilesTransporter(RACKSPACE_USER, RACKSPACE_KEY, array(
                'region' => CF_REGION
            ));

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Check CloudFiles credentials.
     */
    protected function checkCloudFiles() {
        if (!RACKSPACE_USER || !RACKSPACE_KEY) {
            $this->markTestSkipped('Please provide Rackspace access credentials to run these tests');
        }

        if (!CF_CONTAINER || !CF_REGION) {
            $this->markTestSkipped('Please provide a CloudFiles container and region to run these tests');
        }
    }

}
