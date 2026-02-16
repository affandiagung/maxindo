<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_cost extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_cost";
		$this->params['table'] = "projectcosts";
		$this->params['sql'] = "SELECT PROJECTCOSTID,
		PROJECT, DESCRIPTION, COST
		FROM projectcosts
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";

		$this->params['query-total'] = "SELECT SUM(COST) as COST FROM projectcosts WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTCOSTID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'DESCRIPTION' => array(
				'class' => "sorting",
				'sumtitle' => true
			),		
			'COST' => array(
				'class' => "sorting",
				'type' => "number",
				'width' => "150px",
				'sum' => true
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTCOSTID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'DESCRIPTION' => array(
				'type' => "textarea",
				'validation' => "required",
				'class' => "col-md-6",
			),
			'COST' => array(
				'class' => "col-md-3",
				'validation' => "required",
				'type' => "number",
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
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
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
