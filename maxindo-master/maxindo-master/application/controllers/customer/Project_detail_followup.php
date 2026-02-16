<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_detail_followup extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse";
		$this->params['name'] = $this->lang->line("project_detail_followup");
		$this->params['maincontent'] = "project_detail_followup";
		$this->params['simpleform'] = true;
		$this->params['table'] = "projectfollowups";
		$this->params['sql'] = "SELECT PROJECTFOLLOWUPID,projects.NAME PROJECT, FOLLOWUPDATE, NOTES, CONTACTPERSON FROM projectfollowups
		LEFT JOIN projects ON PROJECTID = PROJECT
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."'
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

	
}
