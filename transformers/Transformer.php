<?php

namespace mjohnson\transit\transformers;

interface Transformer {

	/**
	 * Transform the image using the defined options.
	 *
	 * @access public
	 * @param array $options
	 * @return \mjohnson\transit\File
	 */
	public function process(array $options);

	/**
	 * Calculate the transformation options and process.
	 *
	 * @access public
	 * @return string
	 */
	public function transform();

}