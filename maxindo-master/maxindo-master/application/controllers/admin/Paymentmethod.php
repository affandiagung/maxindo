<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paymentmethod extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("paymentmethod");
		$this->params['table'] = "paymentmethods";
		$this->params['sql'] = "SELECT * FROM paymentmethods
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
			'PAYMENTMETHODID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => "sorting",
				'type' => "",

			),
			'ISDEFAULT' => array(
				'class' => "sorting",
				'type' => "",

			),
			'FEE' => array(
				'class' => "sorting",
				'type' => "decimal",

			),
			'PERCENTFEE' => array(
				'class' => "sorting",
				'type' => "decimal",

			),
			'TAX' => array(
				'class' => "sorting",
				'type' => "decimal",

			),
			'PERCENTTAX' => array(
				'class' => "sorting",
				'type' => "decimal",

			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PAYMENTMETHODID' => array(
				'class' => "col-md-6",
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => "col-md-6",
				'type' => "",

			),
			'ISDEFAULT' => array(
				'class' => "col-md-6",
				'type' => "",

			),
			'FEE' => array(
				'class' => "col-md-6",
				'type' => "decimal",

			),
			'PERCENTFEE' => array(
				'class' => "col-md-6",
				'type' => "decimal",

			),
			'TAX' => array(
				'class' => "col-md-6",
				'type' => "decimal",

			),
			'PERCENTTAX' => array(
				'class' => "col-md-6",
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
