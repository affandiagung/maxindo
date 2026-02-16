<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_invoice_master extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->urisegments = $this->uri->uri_to_assoc(4);
		//Do your magic here
	}

	public function index(){
		$this->load->view("admin/project_invoice_master");
	}

}

/* End of file Project_detail.php */
/* Location: ./application/controllers/admin/Project_detail.php */