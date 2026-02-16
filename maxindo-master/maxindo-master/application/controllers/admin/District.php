<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class District extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("district");
		$this->params['table'] = "districts";
		$this->params['sql'] = "SELECT DISTRICTID, districts.NAME, 
		cities.NAME as CITY,
		provinces.NAME as PROVINCE
		FROM districts
		LEFT JOIN cities ON CITY=CITYID
		LEFT JOIN provinces ON PROVINCE=PROVINCEID
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
			'DISTRICTID' => array(
				'type' => "primarykey",
				'width' => "100px"
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'CITY' => array(
				'class' => "sorting",
				'width' => "100px"
			),
			'PROVINCE' => array(
				'class' => "sorting",
				'width' => "100px"
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'DISTRICTID' => array(
				'type' => "primarykey",
				'validation' => "required",
				'class' => "col-md-6",
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'CITY' => array(
				'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getCity())
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
		if( count($_POST) > 0 ){
			$_POST['ADMSTATUS'] = '4';
		}
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
	
}
?>