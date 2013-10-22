<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\File;

/**
 * Interface for all transporters to implement.
 *
 * @package Transit
 */
interface Transporter extends Component {

    /**
     * Delete a file from the remote location.
     *
     * @param string $id
     * @return bool
     */
    public function delete($id);

    /**
     * Transport the file to a remote location.
     *
     * @param \Transit\File $file
     * @param array $config
     * @return string
     */
    public function transport(File $file, array $config = array());

}