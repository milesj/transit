<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transporters;

use mjohnson\transit\File;
use mjohnson\transit\exceptions\TransportationException;
use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Enum\Storage;
use Aws\S3\Exception\S3Exception;
use Aws\Common\Enum\Region;
use Guzzle\Http\EntityBody;
use \InvalidArgumentException;

/**
 * Transport a local file to Amazon S3.
 *
 * @package	mjohnson.transit.transporters
 */
class S3Transporter extends AbstractTransporter {

	const S3_URL = 'https://s3.amazonaws.com';
	const S3_DOMAIN = 's3.amazonaws.com';

	/**
	 * S3Client instance.
	 *
	 * @access protected
	 * @var \Aws\S3\S3Client
	 */
	protected $_s3;

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'bucket' => '',
		'folder' => '',
		'storage' => Storage::STANDARD,
		'acl' => CannedAcl::PUBLIC_READ,
		'encryption' => 'AES256',
		'meta' => array()
	);

	/**
	 * Instantiate an S3Client object.
	 *
	 * @access public
	 * @param string $accessKey
	 * @param string $secretKey
	 * @param string|array $config
	 * @param array $options
	 * @throws \InvalidArgumentException
	 */
	public function __construct($accessKey, $secretKey, $config = array(), array $options = array()) {
		if (is_string($config)) {
			$config = array('bucket' => $config);
		}

		if (!$config['bucket']) {
			throw new InvalidArgumentException('Please provide an S3 bucket');
		}

		$options = $options + array('region' => Region::US_WEST_1);
		$options['key'] = $accessKey;
		$options['secret'] = $secretKey;

		$this->_s3 = S3Client::factory($options);

		parent::__construct($config);
	}

	/**
	 * Delete a file from the remote location.
	 *
	 * @access public
	 * @param string $path
	 * @return boolean
	 */
	public function delete($path) {
		$path = $this->parseUrl($path);

		return (bool) $this->_s3->deleteObject(array(
			'Bucket' => $path['bucket'] ?: $this->_config['bucket'],
			'Key' => $path['key']
		));
	}

	/**
	 * Transport the file to a remote location.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @return string
	 * @throws \mjohnson\transit\exceptions\TransportationException
	 * @throws \InvalidArgumentException
	 */
	public function transport(File $file) {
		$config = $this->_config;
		$options = array(
			'Bucket' => $config['bucket'],
			'ACL' => $config['acl'],
			'Key' => $config['folder'] . $file->basename(),
			'Body' => EntityBody::factory(fopen($file->path(), 'r')),
			'ContentType' => $file->type(),
			'ServerSideEncryption' => $config['encryption'],
			'StorageClass' => $config['storage'],
			'Metadata' => $config['meta']
		);

		if ($response = $this->_s3->putObject($options)) {
			$file->delete();

			return sprintf('%s/%s/%s', self::S3_URL, $options['Bucket'], trim($options['Key'], '/'));
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
		$bucket = '';
		$key = $url;

		if (strpos($url, self::S3_DOMAIN) !== false) {

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