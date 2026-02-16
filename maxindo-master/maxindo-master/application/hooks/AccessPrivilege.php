<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccessPrivilege {

	private $params=array();
	private $classPath="";
	private $loggedAdmin=array();
	private $loggedMember=array();
	private $siteurl=array();
	
	public function __construct(){
		$this->ci = &get_instance();
		$this->urisegments = $this->ci->uri->uri_to_assoc(4);
		$this->loggedAdmin = $this->ci->session->userdata("admin");
		$this->loggedMember = $this->ci->session->userdata("nik");
		if (defined('STDIN')){
			$this->siteurl= '';
		} else {
			// $protocol = $_SERVER['REQUEST_SCHEME'];
			if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
				$this->siteurl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "/";
			}
			else {
				$this->siteurl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "/";
			}
		}
	}

	function checkPrivilege(){
		$this->ci->load->model("Mmasterdata");
		$dir = $this->ci->router->fetch_directory();
		if( $dir != "" ){
			$this->checkSession();
			$access = $this->ci->Mmasterdata->getAccessPrivilege( $this->loggedAdmin['PRIVILEGE']);
			if($dir != $access."/"){
				// header("location:" . $this->siteurl);
				echo "<script type='text/javascript'>window.location.replace('". $this->siteurl . "');</script>";
				redirect($this->siteurl);
			}
		}
	}

	function checkSession(){
		if($this->loggedAdmin == null && $this->loggedMember == null){
			// header("location:" . $this->siteurl . "logout");
			echo "<script type='text/javascript'>window.location.replace('".$this->siteurl . "logout');</script>";
			redirect($this->siteurl . "logout");
		}
	}
}