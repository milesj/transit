<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\transporters\aws;

use mjohnson\transit\File;
use mjohnson\transit\exceptions\TransportationException;
use Aws\Glacier\GlacierClient;
use Aws\Glacier\Exception\GlacierException;
use Aws\Common\Enum\Region;
use Guzzle\Http\EntityBody;
use \InvalidArgumentException;

/**
 * Transport a local file to Amazon Glacier.
 *
 * @package	mjohnson.transit.transporters.aws
 */
class GlacierTransporter extends AbstractAwsTransporter {

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
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
	 * @access public
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

		$this->_client = GlacierClient::factory($this->_config);
	}

	/**
	 * Delete a file from Amazon Glacier using the archive ID.
	 *
	 * @access public
	 * @param string $id
	 * @return boolean
	 */
	public function delete($id) {
		$config = $this->_config;

		try {
			$this->_client->deleteArchive(array_filter(array(
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
	 * @access public
	 * @param \mjohnson\transit\File $file
	 * @return string
	 * @throws \mjohnson\transit\exceptions\TransportationException
	 * @throws \InvalidArgumentException
	 */
	public function transport(File $file) {
		$config = $this->_config;
		$options = array(
			'vaultName' => $config['vault'],
			'accountId' => $config['accountId'],
			'body' => EntityBody::factory(fopen($file->path(), 'r')),
		);

		if ($response = $this->_client->uploadArchive(array_filter($options))) {
			$file->delete();

			return $response->getPath('archiveId');
		}

		throw new TransportationException(sprintf('Failed to transport %s to Amazon Glacier', $file->basename()));
	}

}