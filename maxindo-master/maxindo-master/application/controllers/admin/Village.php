<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Village extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("village");
		$this->params['table'] = "villages";
		$this->params['sql'] = "SELECT VILLAGEID, villages.NAME, 
		districts.NAME as DISTRICT,admstatuses.NAME as ADMSTATUS
		FROM villages
		LEFT JOIN districts ON DISTRICT=DISTRICTID
		LEFT JOIN cities ON CITY=CITYID
		LEFT JOIN admstatuses ON villages.ADMSTATUS=ADMSTATUSID
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
			'VILLAGEID' => array(
				'type' => "primarykey",
				'width' => "100px"
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'DISTRICT' => array(
				'class' => "sorting",
				'width' => "100px"
			),
			'ADMSTATUS' => array(
				'class' => "sorting",
				'width' => "100px"
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'VILLAGEID' => array(
				'hidden' => true,
				'type' => "primarykey"
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'DISTRICT' => array(
				'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getCity()
			),
			'ADMSTATUS' => array(
				'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getAdmStatus( array(5,6) )
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