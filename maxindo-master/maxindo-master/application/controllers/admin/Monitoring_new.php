<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$data = array();
		$data['module'] = "admin";
		$this->load->view("admin/monitoring", $data);
	}

}

/* End of file Monitoring.php */
/* Location: ./application/controllers/admin/Monitoring.php */