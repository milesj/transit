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
	public function transform(array $options);

	/**
	 * Calculate the transformation options.
	 *
	 * @access public
	 * @return array
	 */
	public function process();

}