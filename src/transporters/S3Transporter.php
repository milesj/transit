<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transporters;

use mjohnson\transit\File;
use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Enum\Storage;
use Aws\Common\Enum\Region;
use \Exception;

/**
 * @todo
 *
 * @package	mjohnson.transit.transporters
 */
class S3Transporter extends AbstractTransporter {

	const S3_URL = 'https://s3.amazonaws.com';

	/**
	 * S3Client instance.
	 *
	 * @access protected
	 * @var \Aws\S3\S3Client
	 */
	protected $_s3;

	/**
	 * Instantiate an S3Client object.
	 *
	 * @access public
	 * @param string $accessKey
	 * @param string $secretKey
	 * @param array $options
	 */
	public function __construct($accessKey, $secretKey, array $options = array()) {
		$options = $options + array('region' => Region::US_WEST_1);
		$options['key'] = $accessKey;
		$options['secret'] = $secretKey;

		$this->_s3 = S3Client::factory($options);
	}

	/**
	 * Delete a file from the remote location.
	 *
	 * @access public
	 * @param string $path
	 * @param array $options
	 * @return boolean
	 * @throws \Exception
	 */
	public function delete($path, array $options = array()) {
		$options = $options + array('bucket' => '');

		if (!$options['bucket']) {
			throw new Exception('Please provide an S3 bucket');
		}

		return (bool) $this->_s3->deleteObject(array(
			'Bucket' => $options['bucket'],
			'Key' => $path
		));
	}

	/**
	 * Transport the file to a remote location.
	 *
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @param array $options
	 * @return string
	 * @throws \Exception
	 */
	public function transport(File $file, array $options = array()) {
		$options = $options + array(
			'bucket' => '',
			'folder' => '',
			'storage' => Storage::STANDARD,
			'acl' => CannedAcl::PUBLIC_READ,
			'encryption' => 'AES256',
			'meta' => array()
		);

		if (!$options['bucket']) {
			throw new Exception('Please provide an S3 bucket');
		}

		$args = array(
			'Bucket' => $options['bucket'],
			'ACL' => $options['acl'],
			'Key' => $options['folder'] . $file->basename(),
			'Body' => fopen($file->path(), 'r'),
			'ContentType' => $file->type(),
			'ServerSideEncryption' => $options['encryption'],
			'StorageClass' => $options['storage'],
			'Metadata' => $options['meta']
		);

		if ($response = $this->_s3->putObject($args)) {
			if ($response->isSuccessful()) {
				return sprintf('%s/%s/%s', self::S3_URL, $args['Bucket'], trim($args['Key'], '/'));
			}
		}

		throw new Exception(sprintf('Failed to transport %s to Amazon S3', $file->basename()));
	}

}