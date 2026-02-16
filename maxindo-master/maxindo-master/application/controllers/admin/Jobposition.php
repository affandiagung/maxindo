<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobposition extends CI_Controller{
	
	private $params = array();
	
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
		$this->params['name'] = $this->lang->line("jobposition");
		$this->params['table'] = "jobpositions";
		$this->params['sql'] = "SELECT JOBPOSITIONID,jobpositions.NAME,units.NAME UNIT,JOBDESCRIPTION,QUALIFICATION,BASESALARY FROM jobpositions
		LEFT JOIN units ON UNITID = UNIT
		";
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
			'JOBPOSITIONID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'UNIT' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'JOBDESCRIPTION' => array(
				'class' => 'sorting',
				'type' => "textarea",
			),
			'QUALIFICATION' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'BASESALARY' => array(
				'class' => 'sorting',
				'type' => "decimal",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'JOBPOSITIONID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'type' => "",
			),
			'UNIT' => array(
				'class' => 'col-md-6 select2',
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getUnit()),
			),
			'JOBDESCRIPTION' => array(
				'class' => 'col-md-6',
				'type' => "textarea",
			),
			'QUALIFICATION' => array(
				'class' => 'col-md-6',
				'type' => "",
				'type' => "textarea",
			),
			'BASESALARY' => array(
				'class' => 'col-md-6',
				'type' => "decimal",
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
		echo "<script type='text/javascript'>
			
		</script>";
	}

	function add(){

		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
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
			loadcontent('engine-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
		</script>";
	}
	
}
