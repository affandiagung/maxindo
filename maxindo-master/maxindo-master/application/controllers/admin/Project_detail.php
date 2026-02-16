<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_detail extends CI_Controller {

	private $logged = array();

	public function __construct(){
		parent::__construct();
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->logged = $this->session->userdata("admin");
		//Do your magic here
	}

	public function index(){
		$data['projectstages'] = $this->Mmasterdata->getProjectStage();
		if( isset($this->urisegments['valpk']) ){
			$this->session->set_userdata("PROJECTID", $this->urisegments['valpk']);
		}
		if( $this->session->has_userdata("PROJECTID")){
			$project = $this->Mmasterdata->getProjectDetail( $this->session->userdata("PROJECTID") );
			$this->session->set_userdata("project", $project);
			$data['project'] = $project;
			$data['PRIVILEGE'] = $this->logged['PRIVILEGE'];
			$this->load->view("admin/project_detail", $data);
		} else {
			echo "Invalid parameter";
		}
	}

	function updateStage(){
		$post = $this->input->post();
		$project = $this->db->where("PROJECTID", $this->session->userdata("PROJECTID"))->get("projects")->row();
		if (!empty($post)) {
			if ($post['stage'] >= "4" && $project->APPROVALSTATUS != "1") {
				$_POST=array();
				$this->params['alert'] = array(
					'status' => false,
					'type' => "warning",
					'message' => "Quotation belum di approve oleh marketing manager!",
				);
			}
			else{
				$this->db->update("projects", array('PROJECTSTAGE' => $post['stage']), array('PROJECTID' => $this->session->userdata("PROJECTID") ));
				$this->params['alert'] = array(
					'status' => true,
					'type' => "success",
					'message' => "Berhasil.",
				);
			}
			echo json_encode($this->params['alert']);
		}
	}

}

/* End of file Project_detail.php */
/* Location: ./application/controllers/admin/Project_detail.php */