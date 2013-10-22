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

class S3TransporterTest extends TestCase {

    /**
     * Test that uploading a file to S3 returns a URL and deleting the file via the URL works.
     */
    public function testTransportAndDelete() {
        $this->checkS3();

        $object = new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
            'bucket' => S3_BUCKET,
            'region' => S3_REGION
        ));

        try {
            copy($this->baseFile, $this->tempFile);

            if ($response = $object->transport(new File($this->tempFile))) {
                $this->assertEquals($response, sprintf('https://s3.amazonaws.com/%s/%s', S3_BUCKET, basename($this->tempFile)));
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
     * Test that parsing S3 URLs returns the bucket and key.
     */
    public function testParseUrl() {
        $this->checkS3();

        $object = new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
            'bucket' => S3_BUCKET,
            'region' => S3_REGION
        ));

        $this->assertEquals($object->parseUrl('filename.jpg'), array(
            'bucket' => S3_BUCKET,
            'key' => 'filename.jpg',
            'region' => 'us-east-1'
        ));

        $this->assertEquals($object->parseUrl('https://s3.amazonaws.com/bucket1/filename.jpg'), array(
            'bucket' => 'bucket1',
            'key' => 'filename.jpg',
            'region' => 'us-east-1'
        ));

        $this->assertEquals($object->parseUrl('https://bucket2.s3.amazonaws.com/filename.jpg'), array(
            'bucket' => 'bucket2',
            'key' => 'filename.jpg',
            'region' => 'us-east-1'
        ));

        $this->assertEquals($object->parseUrl('https://s3.amazonaws.com/bucket1/test/filename.jpg'), array(
            'bucket' => 'bucket1',
            'key' => 'test/filename.jpg',
            'region' => 'us-east-1'
        ));

        $this->assertEquals($object->parseUrl('https://bucket2.s3.amazonaws.com/some/folder/filename.jpg'), array(
            'bucket' => 'bucket2',
            'key' => 'some/folder/filename.jpg',
            'region' => 'us-east-1'
        ));

        $this->assertEquals($object->parseUrl('https://s3-sa-east-1.amazonaws.com/bucket1/filename.jpg'), array(
            'bucket' => 'bucket1',
            'key' => 'filename.jpg',
            'region' => 'sa-east-1'
        ));

        $this->assertEquals($object->parseUrl('https://bucket2.s3-sa-east-1.amazonaws.com/filename.jpg'), array(
            'bucket' => 'bucket2',
            'key' => 'filename.jpg',
            'region' => 'sa-east-1'
        ));
    }

    /**
     * Test that exceptions are thrown if settings are missing.
     */
    public function testExceptionHandling() {
        try {
            new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
                'bucket' => S3_BUCKET
            ));

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
                'region' => S3_REGION
            ));

            $this->assertTrue(false);

        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

}
