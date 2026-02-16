<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activitytype extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("activitytype");
		$this->params['table'] = "activitytypes";
		$this->params['sql'] = "SELECT ACTIVITYTYPEID,activitytypes.NAME, units.NAME AS UNIT FROM activitytypes
		LEFT JOIN units ON units.UNITID = activitytypes.UNIT
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
			'ACTIVITYTYPEID' => array(
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
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'ACTIVITYTYPEID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'type' => "",
				'validation' => "required", 
 			),
			'UNIT' => array(
				'class' => 'col-md-6 select2',
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getUnit()),
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
