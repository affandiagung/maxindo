<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing_target extends CI_Controller{
	
	private $params = array();
	private $logged = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	
	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		if( $this->logged['PRIVILEGE'] == "MRK"){
			$this->params['command'] = "browse";
		}
		$this->params['name'] = $this->lang->line("target");
		$this->params['table'] = "targets";
		$this->params['sql'] = "SELECT * FROM targets";
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
			'TARGETID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => 'sorting',
			),
			'TARGETSTARTDATE' => array(
				'class' => 'sorting',
				'type' => "date",
			),
			'TARGETENDDATE' => array(
				'class' => 'sorting',
				'type' => "date",
			),
			'TARGETPROJECT' => array(
				'class' => 'sorting',
				'type' => "number",
			),
			'TARGETAMOUNT' => array(
				'class' => 'sorting',
				'type' => "number",
			),
			'ACHIEVEDTARGETPROJECT' => array(
				'class' => 'sorting',
				'type' => "number",
			),
			'ACHIEVEDTARGETAMOUNT' => array(
				'class' => 'sorting',
				'type' => "number",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'TARGETID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'type' => "",
			),
			'TARGETSTARTDATE' => array(
				'class' => 'col-md-3',
				'type' => "date",
				'validation' => "required"
			),
			'TARGETENDDATE' => array(
				'class' => 'col-md-3',
				'type' => "date",
				'validation' => "required"
			),
			'TARGETPROJECT' => array(
				'class' => 'col-md-3',
				'type' => "number",
				'validation' => "required"
			),
			'TARGETAMOUNT' => array(
				'class' => 'col-md-3',
				'type' => "number",
				'validation' => "required"
			),
			'ACHIEVEDTARGETPROJECT' => array(
				'class' => 'col-md-3',
				'type' => "number",
				'disabled' => true
			),
			'ACHIEVEDTARGETAMOUNT' => array(
				'class' => 'col-md-3',
				'type' => "number",
				'disabled' => true
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
