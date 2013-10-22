<?php
/**
 * @copyright    Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license        http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transporter\Aws;

use Transit\File;
use Transit\Exception\TransportationException;
use Aws\Common\Enum\Region;
use Aws\Common\Enum\Size;
use Aws\Common\Exception\MultipartUploadException;
use Aws\Glacier\GlacierClient;
use Aws\Glacier\Exception\GlacierException;
use Aws\Glacier\Model\MultipartUpload\UploadBuilder;
use Guzzle\Http\EntityBody;
use \InvalidArgumentException;

/**
 * Transport a local file to Amazon Glacier.
 *
 * @package Transit\Transporter\Aws
 * @method \Aws\Glacier\GlacierClient getClient()
 */
class GlacierTransporter extends AbstractAwsTransporter {

    /**
     * Configuration.
     *
     * @type array {
     *         @type string $key        AWS access key
     *         @type string $secret     AWS secret key
     *         @type string $vault      Vault archive to place files in
     *         @type string $accountId  Account ID used for authentication
     *         @type string $region     AWS vault region
     * }
     */
    protected $_config = array(
        'key' => '',
        'secret' => '',
        'vault' => '',
        'accountId' => '',
        'region' => Region::US_EAST_1
    );

    /**
     * Instantiate a GlacierClient object.
     *
     * @uses Aws\Glacier\GlacierClient
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($accessKey, $secretKey, array $config = array()) {
        if (empty($config['vault'])) {
            throw new InvalidArgumentException('Please provide a Glacier vault');
        }

        parent::__construct($accessKey, $secretKey, $config);

        $this->_client = GlacierClient::factory($this->getConfig());
    }

    /**
     * Delete a file from Amazon Glacier using the archive ID.
     *
     * @param string $id
     * @return bool
     */
    public function delete($id) {
        $config = $this->getConfig();

        try {
            $this->getClient()->deleteArchive(array_filter(array(
                'vaultName' => $config['vault'],
                'accountId' => $config['accountId'],
                'archiveId' => $id
            )));
        } catch (GlacierException $e) {
            return false;
        }

        return true;
    }

    /**
     * Transport the file to Amazon Glacier and return the archive ID.
     *
     * @uses Aws\Glacier\GlacierClient
     * @uses Aws\Glacier\Model\MultipartUpload\UploadBuilder
     * @uses Guzzle\Http\EntityBody
     *
     * @param \Transit\File $file
     * @param array $config
     * @return string
     * @throws \Transit\Exception\TransportationException
     */
    public function transport(File $file, array $config = array()) {
        $config = $config + $this->getConfig();
        $response = null;

        // If larger then 100MB, split upload into parts
        if ($file->size() >= (100 * Size::MB)) {
            $uploader = UploadBuilder::newInstance()
                ->setClient($this->getClient())
                ->setSource($file->path())
                ->setVaultName($config['vault'])
                ->setAccountId($config['accountId'] ?: '-')
                ->setPartSize(10 * Size::MB)
                ->build();

            try {
                $response = $uploader->upload();
            } catch (MultipartUploadException $e) {
                $uploader->abort();
            }

        } else {
            $response = $this->getClient()->uploadArchive(array_filter(array(
                'vaultName' => $config['vault'],
                'accountId' => $config['accountId'],
                'body' => EntityBody::factory(fopen($file->path(), 'r')),
            )));
        }

        // Return archive ID if successful
        if ($response) {
            $file->delete();

            return $response->getPath('archiveId');
        }

        throw new TransportationException(sprintf('Failed to transport %s to Amazon Glacier', $file->basename()));
    }

}