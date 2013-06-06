<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace Transit;

use Transit\File;

/**
 * Interface for all transformers to implement.
 *
 * @package Transit
 */
interface Transformer {

	/**
	 * Get all config or a single config.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getConfig($key = null);

	/**
	 * Set configuration.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return \Transit\Transformer
	 */
	public function setConfig($key, $value);

	/**
	 * Transform a file by running filters and returning a new File object.
	 *
	 * @param \Transit\File $file
	 * @param bool $self
	 * @return \Transit\File
	 */
	public function transform(File $file, $self = false);

}