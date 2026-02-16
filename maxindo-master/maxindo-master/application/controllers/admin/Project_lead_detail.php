<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_lead_detail extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->urisegments = $this->uri->uri_to_assoc(4);
		//Do your magic here
	}

	public function index(){
		if( isset($this->urisegments['valpk']) ){
			$this->session->set_userdata("PROJECTID", $this->urisegments['valpk']);
		}
		if( $this->session->has_userdata("PROJECTID")){
			$project = $this->db->select("projects.*, projectstages.CLASS, projectstages.NAME as PROJECTSTAGENAME, customers.NAME as CUSTOMERNAME, employees.NAME as EMPLOYEENAME")
			->where("PROJECTID", $this->session->userdata("PROJECTID"))
			->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
			->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
			->join("projectstages", "PROJECTSTAGE=PROJECTSTAGEID", "LEFT")
			->get("projects")->row();
			$data['project'] = $project;
			$this->load->view("admin/project_lead_detail", $data);
		} else {
			echo "Invalid parameter";
		}
	}

}

/* End of file Project_detail.php */
/* Location: ./application/controllers/admin/Project_detail.php */