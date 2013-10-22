<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Test;

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * Class instance.
     *
     * @var object
     */
    protected $object;

    /**
     * Mock $_FILES data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Base file for testing.
     *
     * @var string
     */
    protected $baseFile;

    /**
     * Temporary file for uploading.
     *
     * @var string
     */
    protected $tempFile;

    /**
     * Create temporary test files to mimic file uploading.
     */
    protected function setUp() {
        $baseFile = TEMP_DIR . '/vertical.jpg';
        $tempFile = TEMP_DIR . '/vertical-test.jpg';

        $this->baseFile = $baseFile;
        $this->tempFile = $tempFile;
        $this->data = array(
            'name' => basename($tempFile),
            'type' => 'image/jpeg',
            'tmp_name' => $baseFile, // Use base to copy over to test
            'error' => 0,
            'size' => filesize($baseFile)
        );
    }

    /**
     * Delete the temporary file.
     */
    protected function tearDown() {
        foreach (glob(TEMP_DIR . '/vertical-*.jpg') as $file) {
            //@unlink($file);
        }

        foreach (glob(TEMP_DIR . '/horizontal-*.jpg') as $file) {
            //@unlink($file);
        }

        clearstatcache();
    }

    /**
     * Check S3 credentials.
     */
    protected function checkS3() {
        if (!AWS_ACCESS || !AWS_SECRET) {
            $this->markTestSkipped('Please provide AWS access credentials to run these tests');
        }

        if (!S3_BUCKET || !S3_REGION) {
            $this->markTestSkipped('Please provide an S3 bucket and region to run these tests');
        }
    }

    /**
     * Check Glacier credentials.
     */
    protected function checkGlacier() {
        if (!AWS_ACCESS || !AWS_SECRET) {
            $this->markTestSkipped('Please provide AWS access credentials to run these tests');
        }

        if (!GLACIER_VAULT || !GLACIER_REGION) {
            $this->markTestSkipped('Please provide a Glacier vault and region to run these tests');
        }
    }

}
