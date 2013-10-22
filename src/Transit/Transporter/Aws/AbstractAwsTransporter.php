<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transporter\Aws;

use Transit\Transporter\AbstractTransporter;
use \InvalidArgumentException;

/**
 * Base class for all AWS transporters to extend.
 *
 * @package Transit\Transporter\Aws
 */
abstract class AbstractAwsTransporter extends AbstractTransporter {

    /**
     * Client instance.
     *
     * @type object
     */
    protected $_client;

    /**
     * Instantiate an AWS client object.
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($accessKey, $secretKey, array $config = array()) {
        if (empty($config['region'])) {
            throw new InvalidArgumentException('Please provide an AWS region');
        }

        $config['key'] = $accessKey;
        $config['secret'] = $secretKey;

        parent::__construct($config);
    }

    /**
     * Return the client.
     *
     * @return \Aws\Common\Client\AbstractClient
     */
    public function getClient() {
        return $this->_client;
    }

}