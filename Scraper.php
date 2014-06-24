<?php

/**
 * This class provides a simple mechanism for "scraping" websites.
 * 
 * @author jstormes
 *
 */
class Web_Scraper {

	public $cookie_file = '';


	/**
	 * This class provides a simple mechanism for "scraping" websites.
	 * Maintains cookies in a cookie file.
	 *
	 * By: jstormes Mar 7, 2014
	 *
	 * @param array $options
	 */
	function __construct ($options=array()) {
		
		// Disable warnings and error messages from DOMDocument().
		libxml_use_internal_errors(true);
		
		$defaults = array(
			'cookie_name'=>'site.com',
			'cookie_path'=>'/../../data/cookies/'
		);
		
		$NewOptions = array_merge($defaults, $options);

		
		$this->cookie_file = dirname(__file__) . $NewOptions['cookie_path'].$NewOptions['cookie_name'];
	}
	
	
	/**
	 * Delete cookie file.
	 *
	 * By: jstormes Mar 17, 2014
	 *
	 */
	public function delete_cookie() {
		@unlink($this->cookie_file);
	}


	/**
	 * Gets a "page" of data from a url, if $post is supplied it is
	 * used as the form values for postback.
	 *
	 * By: jstormes Mar 13, 2014
	 *
	 * @param string $url
	 * @param string|array|Web_Page $post
	 * @throws Exception
	 * @return Web_Page
	 */
	public function get_page($url, $post=null) {
		
		if ($post==null)
			return $this->post_page($url);
		
		if (is_array($post))
			return $this->post_page($url,$post);
		
		if (is_object($post))
			if (property_exists($post,"form_fields"))
				return $this->post_page($url,$post->form_fields);
			
		if (is_string($post))
			return $this->post_page($url,$post);
			
		throw new Exception('Unknown $post object type passed to get_page()');
	}

	
	/**
	 * Post form values to a page and returns the the page. 
	 *
	 * By: jstormes Mar 13, 2014
	 *
	 * @param unknown $url
	 * @param string $post
	 * @return Web_Page
	 */
	public function post_page($url, $post=null) {
		
		// Turn array into post string.
		if (is_array($post)) {
			$postdata='';
			foreach ($post as $name=>$value)
				$postdata .= $name."=".rawurlencode($value)."&";
			$postdata=rtrim($postdata,"&");
			
			$post=$postdata;
		}
		
		$page = new Web_Page();
		
		$ch = curl_init();
		
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->cookie_file);   // Cookie management.
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookie_file);

		if ($post!=null) {
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt ($ch, CURLOPT_POST, true);
		}
		
		$page->html = curl_exec ($ch);
	
		curl_close($ch);
		
		$doc = new DOMDocument();
		$doc->loadHTML($page->html);
		$nodes = $doc->getElementsByTagName('input');
			
		for($i=0; $i<$nodes->length; $i++) {
			$name = $nodes->item($i)->getAttribute('name');
			$value = $nodes->item($i)->getAttribute('value');
			//$type = $nodes->item($i)->getAttribute('type');
			$page->form_fields[$name]=$value;
		}
		
		
		return $page;
	}

	

	/**
	 * Parses a table from a page returning an array of the values from the
	 * table.
	 *
	 * By: jstormes Mar 13, 2014
	 *
	 * @param Web_Page $page 
	 * @param number $table_idx index of table to parse
	 * @return multitype:multitype:string
	 */
	public function get_table_by_idx($page,$table_idx=0) {
		
		$results = array();
		
		try {
			$doc = new DOMDocument();
			$doc->loadHTML($page->html);
			
			/*** discard white space ***/
		    $doc->preserveWhiteSpace = false;
		
		    /*** the table by its tag name ***/
		    $tables = $doc->getElementsByTagName('table');
		
		    if ($tables->length > $table_idx) {
			    /*** get all rows from the table ***/
			    $rows = $tables->item($table_idx)->getElementsByTagName('tr');
			
			    /*** loop over the table rows ***/
			    foreach ($rows as $row)
			    {
			        /*** get each column by tag name ***/
			        $cols = $row->getElementsByTagName('td');
			        
			        if ($cols->length==0)
			        	$cols = $row->getElementsByTagName('th');
			        
			        $r = array();
			        for($i=0; $i<$cols->length; $i++ ){
			        	$r[$i]=trim($cols->item($i)->nodeValue);
			        }
			        $results[] = $r;
			    } 
		    }
		}
	    catch (Exception $e) {
	    	
	    }
	    
	    return $results;
	}


}