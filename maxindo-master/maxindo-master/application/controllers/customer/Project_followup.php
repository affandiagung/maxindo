<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_followup extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		if( isset($this->urisegments['valpk']) ){
		      $this->session->set_userdata('project_followup_where', $this->uri->uri_to_assoc(4) );
		}
		if( !$this->session->has_userdata("project_followup_where") ){
		      echo "Invalid parameters !";exit;
		}
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse";
		$this->params['name'] = $this->lang->line("projectfollowup");
		$this->params['maincontent'] = "projectfollowups";
		$this->params['simpleform'] = true;
		$this->params['table'] = "projectfollowups";
		$this->params['sql'] = "SELECT PROJECTFOLLOWUPID,projects.NAME PROJECT, FOLLOWUPDATE, NOTES, CONTACTPERSON FROM projectfollowups
		LEFT JOIN projects ON PROJECTID = PROJECT
		WHERE 1 
		";
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
	
	
	function getData(){
		$where = $this->session->userdata()['project_followup_where']['pk']." = ".$this->session->userdata()['project_followup_where']['valpk'];
		$this->params['sql'] .=  " AND ".$where;
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "PROYEK : " . $this->Mmasterdata->getProjectNameById($this->session->userdata()['project_followup_where']['valpk'])
		);
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}
	
}
