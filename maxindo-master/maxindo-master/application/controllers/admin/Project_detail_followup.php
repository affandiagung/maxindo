<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_detail_followup extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		if( $this->logged['PRIVILEGE'] == "MRK" ){
			$project = $this->session->userdata("project");
			if( $this->logged['EMPLOYEE'] != $project->EMPLOYEE ){
				$this->params['command'] = "browse";
			}
		}
		$this->params['name'] = $this->lang->line("project_detail_followup");
		$this->params['maincontent'] = "project_detail_followup";
		$this->params['simpleform'] = true;
		$this->params['table'] = "projectfollowups";
		$this->params['sql'] = "SELECT PROJECTFOLLOWUPID,projects.NAME PROJECT, FOLLOWUPDATE, NOTES, CONTACTPERSON, projectfollowups.CREATEBY, projectfollowups.UPDATEBY, projectfollowups.CREATEAT, projectfollowups.UPDATEAT FROM projectfollowups
		LEFT JOIN projects ON PROJECTID = PROJECT
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."'
		";
		$this->params['order'] = "FOLLOWUPDATE DESC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTFOLLOWUPID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'FOLLOWUPDATE' => array(
				'class' => 'sorting',
				'type' => "date",
			),
			'NOTES' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'CONTACTPERSON' => array(
				'class' => 'sorting',
				'type' => "",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTFOLLOWUPID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'FOLLOWUPDATE' => array(
				'class' => 'col-md-6',
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'NOTES' => array(
				'class' => 'col-md-6',
				'type' => "textarea",
			),
			'CONTACTPERSON' => array(
				'class' => 'col-md-6',
				'type' => "textarea",
			),
			'CREATEBY' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'CREATEAT' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'UPDATEBY' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'UPDATEAT' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){

		/*$this->params['alert'] = array(
			'type' => "primary",
			'message' => "PROYEK : " . $this->Mmasterdata->getProjectNameById($this->session->userdata("PROJECTID"))
		);*/

		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['UPDATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
			</script>";
		}
	}

	function deleteall(){
		$post = $this->input->post();
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$this->db->delete($this->params['table'], array($pk => $val));
			}
		}
		echo "<script>
			loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
		</script>";
	}
	
}
