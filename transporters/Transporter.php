<?php

namespace mjohnson\transit\transporters;

interface Transporter {

	/**
	 * Transport the file to a remote location.
	 *
	 * @access public
	 * @return string
	 */
	public function transport();

}