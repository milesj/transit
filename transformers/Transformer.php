<?php

namespace mjohnson\transit\transformers;

interface Transformer {

	/**
	 * Transform the image using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return boolean
	 */
	public function process(array $options);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @return array
	 */
	public function transform();

}