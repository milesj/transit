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
use Aws\Common\Enum\Region;
use \InvalidArgumentException;

/**
 * Transport a local file to Amazon Glacier.
 *
 * @package	mjohnson.transit.transporters.aws
 */
class GlacierTransporter extends AbstractAwsTransporter {

	const GLACIER_URL = '';
	const GLACIER_HOST = '';

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
		$options = array();

		if ($response = $this->_client->uploadArchive($options)) {
			$file->delete();

			return ''; // @todo
		}

		throw new TransportationException(sprintf('Failed to transport %s to Amazon Glacier', $file->basename()));
	}

	/**
	 * @todo
	 *
	 * @access public
	 * @param string $url
	 * @return array
	 */
	public function parseUrl($url) {
		return array();
	}

}