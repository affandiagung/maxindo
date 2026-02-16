<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CheckSession {

	private $siteurl=array();
	
	public function __construct(){
		$this->ci = &get_instance();
		if (defined('STDIN')){
			
		} else {
			$protocol = $_SERVER['SERVER_PROTOCOL'];
			if( substr($protocol,0,5 == "HTTPS" ) ){
				$this->siteurl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "/";
			}
			else {
				$this->siteurl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "/";
			}
		}
	}

	function checkSession(){
		// print_r($_SESSION['order']);
	}
}