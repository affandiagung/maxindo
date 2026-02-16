<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_calendar extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("inventorycalendar");
		$this->params['table'] = "inventorycalendars";
		$this->params['sql'] = "SELECT 
		INVENTORYCALENDARID, inventories.NAME INVENTORY, STARTDATE, ENDDATE, 
		PROJECTORDERCODE, customers.NAME as CUSTOMER, 
		USEDCOUNT, DESCRIPTION,
		projects.PROJECTLOCATION
		FROM inventorycalendars
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN inventories ON INVENTORYID = INVENTORY
		LEFT JOIN customers ON CUSTOMERID = CUSTOMER
		";
		$this->params['order'] = "INVENTORYCALENDARID DESC"; 
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
			'INVENTORYCALENDARID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'INVENTORY' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'STARTDATE' => array(
				'class' => 'sorting',
				'type' => "datetime",
			),
			'ENDDATE' => array(
				'class' => 'sorting',
				'type' => "datetime",
			),
			'PROJECTORDERCODE' => array(
				'class' => 'sorting',
			),
			'CUSTOMER' => array(
				'class' => 'sorting',
			),
			'PROJECTLOCATION' => array(
				'class' => 'sorting',
				'type' => "function",
				'func' => "splitLokasi",
				'model' => "Mmasterdata",
				'params' => "PROJECTLOCATION"
			),
			'USEDCOUNT' => array(
				'class' => 'sorting',
				'type' => "number",
			),
			'DESCRIPTION' => array(
				'class' => 'sorting',
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'INVENTORYCALENDARID' => array(
				'class' => 'col-md-3',
				'type' => "primarykey",
				'hidden' => true,
			),
			'INVENTORY' => array(
				'class' => 'col-md-6 select2',
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventory())
			),
			'STARTDATE' => array(
				'validation' => "required",
				'class' => 'col-md-3',
				'type' => "datetime",
			),
			'ENDDATE' => array(
				'validation' => "required",
				'class' => 'col-md-3',
				'type' => "datetime",
			),
			'PROJECT' => array(
				'class' => 'col-md-6 select2',
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjects())
			),
			'USEDCOUNT' => array(
				'validation' => "required",
				'class' => 'col-md-3',
				'type' => "number",
			),
			'DESCRIPTION' => array(
				'class' => 'col-md-6',
				'type' => "textarea",
			)
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
