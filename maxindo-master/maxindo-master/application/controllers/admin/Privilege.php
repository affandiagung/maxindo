<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Privilege extends CI_Controller{

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
		$this->params['command'] = "browse,detail,edit,add,delete";
		$this->params['name']=$this->lang->line("privilege");
		$this->params['table']="privileges";
		$this->params['sql']="
			SELECT * FROM privileges
		";
		// $this->params['order'] = 'CREATEAT DESC';
		$this->urisegments=$this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
		$this->params['sqlmaster'] = $this->params['sql'];

		// Slave Params
		$this->params['table-slave'] ="menuprivileges";
		$this->params['slavecommand'] ="browse,add,edit,delete,deleteall";
		$this->params['slavename'] = $this->lang->line("menuprivilege");
		$this->params['sqlslave']="SELECT 
		menuprivileges.PRIVILEGE,
		menuprivileges.MENUPRIVILEGEID, 
		menus.NAME as MENU,
		parentmenus.NAME as PARENTMENU,
		ORDERSEQ
		FROM menuprivileges
		LEFT JOIN menus ON menuprivileges.MENU=menus.MENUID
		-- LEFT JOIN menuprivileges as parents ON menuprivileges.PARENTMENU=parents.MENUPRIVILEGEID
		LEFT JOIN menus as parentmenus ON menuprivileges.PARENTMENU=parentmenus.MENUID
		";
	}

	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(

			),
			'PRIVILEGEID' => array(
				'type' => "primarykey",
				'class' => "sorting",
			),
			'NAME' => array(
				'class' => "sorting",
			),	
			'HOMEDIR' => array(
				'class' => "sorting",
				'width' => "200px"
			),
		);
	}

	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PRIVILEGEID' => array(
				'type' => "primarykey",
				'class' => "col-md-3",
				'validation' => "required"
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'HOMEDIR' => array(
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

	function fieldselectmaster(){
		$this->params['fieldselectedmaster']=array(
			'NAME' => array(

			),
			'HOMEDIR' => array(

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
			'MENUPRIVILEGEID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'PRIVILEGE' => array(
				'type' => "foreignkey",
				'hidden' => true
			),
			'MENU' => array(
				'class' => "sorting",
			),
			'PARENTMENU' => array(
				'class' => "sorting",
			),
			'ORDERSEQ' => array(
				'class' => "sorting",
			),
		);
	}
	function fieldeditslave(){
		$this->params['fieldeditslave']=array(
			'MENUPRIVILEGEID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'PRIVILEGE' => array(
				'type' => "foreignkey",
				'hidden' => true,
				'value' => $this->urisegments['valpk']
			),
			'PARENTMENU' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => array_merge(array(0 => array("keydt" => "", "valuedt" => "-")), $this->Mmasterdata->getAllMenu())
			),
			'MENU' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => array_merge(array(0 => array("keydt" => "", "valuedt" => "-")), $this->Mmasterdata->getAllMenu())
			),
			'ORDERSEQ' => array(
				'class' => "col-md-6",
				'type' => "number",
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
?>
