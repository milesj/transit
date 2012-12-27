<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
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
 */
class S3Transporter extends AbstractAwsTransporter {

	const S3_URL = 'https://s3.amazonaws.com';
	const S3_HOST = 's3.amazonaws.com';

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'key' => '',
		'secret' => '',
		'bucket' => '',
		'folder' => '',
		'region' => Region::US_EAST_1,
		'storage' => Storage::STANDARD,
		'acl' => CannedAcl::PUBLIC_READ,
		'encryption' => '',
		'meta' => array()
	);

	/**
	 * Instantiate an S3Client object.
	 *
	 * @access public
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

		$this->_client = S3Client::factory($this->_config);
	}

	/**
	 * Delete a file from Amazon S3 by parsing a URL or using a direct key.
	 *
	 * @access public
	 * @param string $id
	 * @return boolean
	 */
	public function delete($id) {
		$params = $this->parseUrl($id);

		try {
			$this->_client->deleteObject(array(
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
	 * @access public
	 * @param \Transit\File $file
	 * @return string
	 * @throws \Transit\Exception\TransportationException
	 */
	public function transport(File $file) {
		$config = $this->_config;
		$key = ltrim($config['folder'], '/') . $file->basename();
		$response = null;

		// If larger then 100MB, split upload into parts
		if ($file->size() >= (100 * Size::MB)) {
			$uploader = UploadBuilder::newInstance()
				->setClient($this->_client)
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
			$response = $this->_client->putObject(array_filter(array(
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

			return sprintf(self::S3_URL . '/%s/%s', $config['bucket'], $key);
		}

		throw new TransportationException(sprintf('Failed to transport %s to Amazon S3', $file->basename()));
	}

	/**
	 * Parse an S3 URL and extract the bucket and key.
	 *
	 * @access public
	 * @param string $url
	 * @return array
	 */
	public function parseUrl($url) {
		$bucket = $this->_config['bucket'];
		$key = $url;

		if (strpos($url, self::S3_HOST) !== false) {

			// s3.amazonaws.com/<bucket>
			if (preg_match('/^https?:\/\/s3\.amazonaws\.com\/(.+?)\/(.+?)$/i', $url, $matches)) {
				$bucket = $matches[1];
				$key = $matches[2];

			// <bucket>.s3.amazonaws.com
			} else if (preg_match('/^https?:\/\/(.+?)\.s3\.amazonaws\.com\/(.+?)$/i', $url, $matches)) {
				$bucket = $matches[1];
				$key = $matches[2];
			}
		}

		return array(
			'bucket' => $bucket,
			'key' => trim($key, '/')
		);
	}

}