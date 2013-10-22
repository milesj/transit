<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transporter\Aws;

use Transit\File;
use Transit\Exception\TransportationException;
use Aws\Common\Enum\Region;
use Aws\Common\Enum\Size;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Enum\Storage;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Guzzle\Http\EntityBody;
use \InvalidArgumentException;

/**
 * Transport a local file to Amazon S3.
 *
 * @package Transit\Transporter\Aws
 * @method \Aws\S3\S3Client getClient()
 */
class S3Transporter extends AbstractAwsTransporter {

    /**
     * Configuration.
     *
     * @type array {
     *         @type string $key        AWS access key
     *         @type string $secret     AWS secret key
     *         @type string $bucket     Bucket name to place files in
     *         @type string $folder     Folder path to prepend to file name
     *         @type string $scheme     HTTP protocol to communicate with
     *         @type string $region     AWS bucket region
     *         @type string $storage    S3 storage method
     *         @type string $acl        S3 ACL rules to use
     *         @type string $encryption Encryption algorithm to use for uploading
     *         @type array $meta        Meta data to apply to S3 files
     *         @type bool $returnUrl    Return the full S3 URL or the S3 key after upload
     * }
     */
    protected $_config = array(
        'key' => '',
        'secret' => '',
        'bucket' => '',
        'folder' => '',
        'scheme' => 'https',
        'region' => Region::US_EAST_1,
        'storage' => Storage::STANDARD,
        'acl' => CannedAcl::PUBLIC_READ,
        'encryption' => '',
        'meta' => array(),
        'returnUrl' => true
    );

    /**
     * Instantiate an S3Client object.
     *
     * @uses Aws\S3\S3Client
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($accessKey, $secretKey, array $config = array()) {
        if (empty($config['bucket'])) {
            throw new InvalidArgumentException('Please provide an S3 bucket');
        }

        parent::__construct($accessKey, $secretKey, $config);

        $this->_client = S3Client::factory($this->getConfig());
    }

    /**
     * Delete a file from Amazon S3 by parsing a URL or using a direct key.
     *
     * @param string $id
     * @return bool
     */
    public function delete($id) {
        $params = $this->parseUrl($id);

        try {
            $this->getClient()->deleteObject(array(
                'Bucket' => $params['bucket'],
                'Key' => $params['key']
            ));
        } catch (S3Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Transport the file to a remote location.
     *
     * @uses Aws\S3\S3Client
     * @uses Aws\S3\Model\MultipartUpload\UploadBuilder
     * @uses Guzzle\Http\EntityBody
     *
     * @param \Transit\File $file
     * @param array $config
     * @return string
     * @throws \Transit\Exception\TransportationException
     */
    public function transport(File $file, array $config = array()) {
        $config = $config + $this->getConfig();
        $key = $file->basename();
        $response = null;

        if ($folder = $config['folder']) {
            $key = trim($config['folder'], '/') . '/' . $key;
        }

        // If larger then 100MB, split upload into parts
        if ($file->size() >= (100 * Size::MB)) {
            $uploader = UploadBuilder::newInstance()
                ->setClient($this->getClient())
                ->setSource($file->path())
                ->setBucket($config['bucket'])
                ->setKey($key)
                ->setMinPartSize(10 * Size::MB)
                ->build();

            try {
                $response = $uploader->upload();
            } catch (MultipartUploadException $e) {
                $uploader->abort();
            }

        } else {
            $response = $this->getClient()->putObject(array_filter(array(
                'Key' => $key,
                'Bucket' => $config['bucket'],
                'Body' => EntityBody::factory(fopen($file->path(), 'r')),
                'ACL' => $config['acl'],
                'ContentType' => $file->type(),
                'ServerSideEncryption' => $config['encryption'],
                'StorageClass' => $config['storage'],
                'Metadata' => $config['meta']
            )));
        }

        // Return S3 URL if successful
        if ($response) {
            $file->delete();

            if ($config['returnUrl']) {
                return sprintf('%s/%s/%s',
                    S3Client::getEndpoint($this->getClient()->getDescription(), $config['region'], $config['scheme']),
                    $config['bucket'],
                    $key);
            }

            return $key;
        }

        throw new TransportationException(sprintf('Failed to transport %s to Amazon S3', $file->basename()));
    }

    /**
     * Parse an S3 URL and extract the bucket and key.
     *
     * @param string $url
     * @return array
     */
    public function parseUrl($url) {
        $region = $this->getConfig('region');
        $bucket = $this->getConfig('bucket');
        $key = $url;

        if (strpos($url, 'amazonaws.com') !== false) {

            // s3<region>.amazonaws.com/<bucket>
            if (preg_match('/^https?:\/\/s3(.+?)?\.amazonaws\.com\/(.+?)\/(.+?)$/i', $url, $matches)) {
                $region = $matches[1] ?: $region;
                $bucket = $matches[2];
                $key = $matches[3];

            // <bucket>.s3<region>.amazonaws.com
            } else if (preg_match('/^https?:\/\/(.+?)\.s3(.+?)?\.amazonaws\.com\/(.+?)$/i', $url, $matches)) {
                $bucket = $matches[1];
                $region = $matches[2] ?: $region;
                $key = $matches[3];
            }
        }

        return array(
            'bucket' => $bucket,
            'key' => trim($key, '/'),
            'region' => trim($region, '-')
        );
    }

}