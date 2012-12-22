<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

namespace mjohnson\transit\handlers;

/**
 * Interface for all handlers to implement.
 *
 * @package	mjohnson.transit.handlers
 */
interface Handler {

	public function process();

}