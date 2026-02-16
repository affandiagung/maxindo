<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_workorder extends CI_Controller {

	private $logged = array();
	private $data = array();

	public function __construct(){
		parent::__construct();
		$this->logged = $this->session->userdata();
		//Do your magic here
	}


	public function index(){
		$this->data['project'] = $this->session->userdata("PROJECTID");
		$this->data['projects'] = $this->Mmasterdata->getProjectDetail( $this->data['project'] );
		$this->data['barangs'] = $this->Mmasterdata->showInventory( $this->data['project'] );
		$this->data['subrent'] = $this->Mmasterdata->showSubrents( $this->data['project'] );
		$this->load->view( "admin/projectworkorder", $this->data);
	}

}

/* End of file Project_workorder.php */
/* Location: ./application/controllers/admin/Project_workorder.php */