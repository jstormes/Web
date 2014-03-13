<?php

/**
 * Simple class reprsenting a webpage.
 * 
 * @author jstormes
 *
 */
class Web_Page {
	/**
	 * Array of form fields found on the page.
	 * NOTE: Assumes there is only one form on the page.
	 * 
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * HTML of the page.
	 * 
	 * @var string
	 */
	public $html = '';
	
}