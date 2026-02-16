<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring_detail extends CI_Controller {

	private $logged = array();

	public function __construct(){
		parent::__construct();
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->logged = $this->session->userdata("admin");
		//Do your magic here
	}

	public function index(){
		if( isset($this->urisegments['valpk']) ){
			$this->session->set_userdata("PROJECTID", $this->urisegments['valpk']);
		}
		if( $this->session->has_userdata("PROJECTID")){
			$project = $this->Mmasterdata->getProjectDetail( $this->session->userdata("PROJECTID") );
			$this->session->set_userdata("project", $project);
			$data['project'] = $project;
			$data['PRIVILEGE'] = $this->logged['PRIVILEGE'];
			$this->load->view("admin/monitoring_detail", $data);
		} else {
			echo "Invalid parameter";
		}
	}

}

/* End of file Project_detail.php */
/* Location: ./application/controllers/admin/Project_detail.php */