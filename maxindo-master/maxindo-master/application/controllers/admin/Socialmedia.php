<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Socialmedia extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete";
		$this->params['name'] = $this->lang->line("socialmedia");
		$this->params['table'] = "socialmedias";
		$this->params['sql'] = "SELECT * FROM socialmedias";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'SOCIALMEDIAID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'URL' => array(
				'class' => "sorting",
			),	
			'ICON' => array(
				'class' => "sorting",
				'ltag' => "<i class='",
				'rtag' => "'></i>"
			),		
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'SOCIALMEDIAID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'ICON' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'URL' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
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
	
}
?>