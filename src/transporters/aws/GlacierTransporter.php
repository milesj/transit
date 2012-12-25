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
use \InvalidArgumentException;

/**
 * Transport a local file to Amazon Glacier.
 *
 * @package	mjohnson.transit.transporters.aws
 */
class GlacierTransporter extends AbstractAwsTransporter {

	const GLACIER_URL = 'https://glacier.%s.amazonaws.com';
	const GLACIER_HOST = 'amazonaws.com';

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
		'region' => Region::US_EAST_1,
		'folder' => ''
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
		parent::__construct($accessKey, $secretKey, $config);

		if (empty($config['vault'])) {
			throw new InvalidArgumentException('Please provide a Glacier vault');
		}

		$this->_client = GlacierClient::factory($this->_config);
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

		try {
			$this->_client->deleteArchive(array(
				'Vault' => $path['vault'],
				'Key' => $path['key']
			));
		} catch (GlacierException $e) {
			return false;
		}

		return true;
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
		$options = array(); // @todo

		if ($response = $this->_client->uploadArchive($options)) {
			$file->delete();

			return sprintf(self::GLACIER_URL . '/%s/%s', $config['region'], $config['vault'], trim($options['Key'], '/'));
		}

		throw new TransportationException(sprintf('Failed to transport %s to Amazon Glacier', $file->basename()));
	}

	/**
	 * Parse a Glacier URL and extract the vault, region and key.
	 *
	 * @access public
	 * @param string $url
	 * @return array
	 */
	public function parseUrl($url) {
		$vault = $this->_config['vault'];
		$region = $this->_config['region'];
		$key = $url;

		if (strpos($url, self::GLACIER_HOST) !== false) {

			// glacier.<region>.amazonaws.com/<vault>
			if (preg_match('/^https?:\/\/glacier\.([-a-z0-9]+)\.amazonaws\.com\/(.+?)\/(.+?)$/i', $url, $matches)) {
				$region = $matches[1];
				$vault = $matches[2];
				$key = $matches[3];
			}
		}

		return array(
			'vault' => $vault,
			'region' => $region,
			'key' => trim($key, '/')
		);
	}

}