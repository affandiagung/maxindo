<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {

	private $error = array();

	function __construct(){
		parent::__construct();
		$this->load->model("Mmasterdata");
	}

	function index(){
		$this->session->unset_userdata('nik');
		$this->session->unset_userdata('admin');
		$this->session->unset_userdata('penduduk');
		$this->session->unset_userdata('kadus');
		redirect( base_url() );
	}

	function admin(){
		$this->session->unset_userdata('admin');
		$this->session->unset_userdata("nik");
		$this->session->unset_userdata("kadus");
		redirect( base_url() );	
	}
}
