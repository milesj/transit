<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\File;

/**
 * Interface for all transformers to implement.
 *
 * @package Transit
 */
interface Transformer extends Component {

    /**
     * Transform a file by running filters and returning a new File object.
     *
     * @param \Transit\File $file
     * @param bool $self
     * @return \Transit\File
     */
    public function transform(File $file, $self = false);

}