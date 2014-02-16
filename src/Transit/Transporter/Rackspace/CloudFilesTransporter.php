<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit\Transporter\Rackspace;

use Guzzle\Http\EntityBody;
use InvalidArgumentException;
use OpenCloud\Common\Constants\Size;
use OpenCloud\ObjectStore\Constants\UrlType;
use OpenCloud\Rackspace;
use Transit\Exception\TransportationException;
use Transit\File;
use Transit\Transporter\AbstractTransporter;
use \Exception;

/**
 * Transport a local file to Rackspace CloudFiles.
 *
 * @package Transit\Transporter\Rackspace
 * @method \OpenCloud\Rackspace getClient()
 */
class CloudFilesTransporter extends AbstractTransporter {

    /**
     * Configuration.
     *
     * @type array {
     *      @type string $username  Rackspace Cloud username
     *      @type string $apiKey    Rackspace Cloud API key
     *      @type string $country   The country the cloud service is located in, should use Rackspace constants
     *      @type string $region    The region the container is in
     *      @type string $container The container name to put objects in
     *      @type string $folder    The folder to prepend to files
     *      @type bool $returnUrl   Return the full CF URL or the key after upload
     *      @type string $urlType   The type of URL to return, should use the UrlType constants
     * }
     */
    protected $_config = array(
        'username' => '',
        'apiKey' => '',
        'country' => '',
        'region' => 'IAD',
        'container' => '',
        'folder' => '',
        'returnUrl' => true,
        'urlType' => UrlType::CDN
    );

    /**
     * Container instance.
     *
     * @type \OpenCloud\ObjectStore\Resource\Container
     */
    protected $_container;

    /**
     * CloudFiles service.
     *
     * @type \OpenCloud\ObjectStore\Service
     */
    protected $_service;

    /**
     * Instantiate the Rackspace client and related objects.
     *
     * @param string $username
     * @param string $apiKey
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($username, $apiKey, array $config = array()) {
        if (empty($config['container'])) {
            throw new InvalidArgumentException('Please provide an OpenFiles container');
        }

        if (empty($config['region'])) {
            throw new InvalidArgumentException('Please provide an OpenFiles region');
        }

        $config['username'] = $username;
        $config['apiKey'] = $apiKey;

        parent::__construct($config);

        $this->_client = new Rackspace($this->getConfig('country') ?: RACKSPACE_US, array('username' => $username, 'apiKey' => $apiKey));
        $this->_service = $this->getClient()->objectStoreService('cloudFiles', $this->getConfig('region'));
        $this->_container = $this->getService()->getContainer($this->getConfig('container'));
    }

    /**
     * Delete a file from RackSpace CloudFiles by using the URL/ID.
     *
     * @param string $id
     * @return bool
     */
    public function delete($id) {
        $object = $this->getContainer()->dataObject();
        $object->setName($id);

        /** @type \Guzzle\Http\Message\Response $response */
        $response = $object->delete();

        return $response->isSuccessful();
    }

    /**
     * Return the container.
     *
     * @return \OpenCloud\ObjectStore\Resource\Container
     */
    public function getContainer() {
        return $this->_container;
    }

    /**
     * Return the service.
     *
     * @return \OpenCloud\ObjectStore\Service
     */
    public function getService() {
        return $this->_service;
    }

    /**
     * Transport the file to a remote location.
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

        $data = EntityBody::factory(fopen($file->path(), 'r'));

        // If larger then 5GB, split upload into parts
        if ($file->size() >= (5 * Size::MB)) {
            $transfer = $this->getContainer()->setupObjectTransfer(array(
                'name' => $key,
                'path' => $data,
                'concurrency' => 4,
                'partSize' => 1.5 * Size::GB
            ));

            try {
                $transfer->transfer();
                $response = $transfer->getManifest();
            } catch (Exception $e) { }

        } else {
            $response = $this->getContainer()->uploadObject($key, $data);
        }

        // Return CloudFiles URL if successful
        if ($response) {
            $file->delete();

            if ($config['returnUrl']) {
                return (string) $response->getPublicUrl($config['urlType']);
            }

            return $key;
        }

        throw new TransportationException(sprintf('Failed to transport %s to Rackspace CloudFiles', $file->basename()));
    }

}