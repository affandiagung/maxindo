<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventorypackage extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,detail";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "inventorypackages";
		$this->params['sql'] = "SELECT * FROM inventorypackages";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		
		$this->getfieldselect();
		$this->getfieldedit();

    $this->params['sqlmaster'] = $this->params['sql'];

		// Slave Params
		$this->params['table-slave'] ="inventorypackagedetails";
		$this->params['slavecommand'] ="browse,add,edit,delete,deleteall";
		$this->params['slavename'] = $this->lang->line("inventorypackagedetail");
		$this->params['sqlslave']="SELECT INVENTORYPACKAGEDETAILID, INVENTORYPACKAGE,
		inventories.NAME as INVENTORY,
		inventorypackagedetails.COST 
		FROM inventorypackagedetails
		LEFT JOIN inventories ON INVENTORY=INVENTORYID";
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'INVENTORYPACKAGEID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'NOTES' => array(
				'class' => "sorting",
			),
			'TOTALCOST' => array(
				'class' => "sorting",
				'type' => "number",
				'width' => "150px"
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'INVENTORYPACKAGEID' => array(
				'hidden' => true,
				'type' => "primarykey"
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'TOTALCOST' => array(
				'class' => "col-md-6",
				'type' => "number",
				'validation' => "required"
			),
			'NOTES' => array(
				'type' => "textarea",
				'class' => "col-md-6",
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

	function fieldselectmaster(){
		$this->params['fieldselectedmaster']=array(
			'NAME' => array(

			),			
		);
	}

	function fieldselectslave(){
		$this->params['fieldselectslave']=array(
			'SEQ' => array(

			),
			'#' => array(
				'type' => "checkbox"
			),
			'INVENTORYPACKAGEDETAILID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'INVENTORYPACKAGE' => array(
				'type' => "foreignkey",
				'hidden' => true
			),
			'INVENTORY' => array(
				'class' => "sorting",
			),
			'TOTITEM' => array(
				'class' => "sorting",
				'type' => "number"
			),
			'COST' => array(
				'class' => "sorting",
				'type' => "number"
			),
		);
	}
	function fieldeditslave(){
		$this->params['fieldeditslave']=array(
			'INVENTORYPACKAGEDETAILID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'INVENTORYPACKAGE' => array(
				'type' => "foreignkey",
				'hidden' => true,
				'value' => $this->urisegments['valpk']
			),
			'INVENTORY' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventory())
			),
			'TOTITEM' => array(
				'class' => "col-md-3",
				'type' => "number",
				'validation' => "required"
			),		
			'COST' => array(
				'class' => "col-md-3",
				'type' => "number",
				'validation' => "required"
			),			
		);
	}

	function browse_detail(){
		$this->params['primarykeymaster']=$this->urisegments['pk'];
		$this->params['valprimarykeymaster']=$this->urisegments['valpk'];
		$this->fieldselectmaster();
		$this->fieldselectslave();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse_detail();
	}
	function add_detail(){
		$this->params['primarykeymaster']=$this->urisegments['pk'];
		$this->params['valprimarykeymaster']=$this->urisegments['valpk'];
		$this->fieldselectmaster();
		$this->fieldeditslave();
		$post = $this->input->post();
		// $this->params['name']="Detail " . $this->lang->line('topicparticipants');
		$this->load->library("Engine",$this->params);
		echo $this->engine->add_detail();
	}

	function edit_detail(){
		$this->params['primarykeymaster']= $this->urisegments['fk'] . "ID";
		$this->params['valprimarykeymaster']=$this->urisegments['valfk'];
		$this->fieldselectmaster();
		$this->fieldeditslave();
		$this->params['command']="browse";
		// $this->params['name']="Detail " . $this->lang->line('topicparticipants');
		$post = $this->input->post();
		$this->load->library("Engine",$this->params);
		echo $this->engine->edit_detail();
	}

	function delete_detail(){
		$delete=$this->db->delete($this->params['table-slave'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse_detail/pk/".$this->urisegments['fk']."ID/valpk/".urldecode($this->urisegments['valfk'])."/');
			</script>";
		}
	}

	function deleteall_slave(){
		$post = $this->input->post();
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$this->db->delete($this->params['table-slave'], array($pk => $val));
			}
		}
		echo "<script>
			loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse_detail/pk/".$this->urisegments['pk']."/valpk/".urldecode($this->urisegments['valpk'])."/');
		</script>";
	}
	
}
