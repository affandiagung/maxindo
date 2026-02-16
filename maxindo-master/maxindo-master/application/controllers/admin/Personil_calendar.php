<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personil_calendar extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("employeecalendar");
		$this->params['table'] = "employeecalendars";
		$this->params['sql'] = "SELECT 
		EMPLOYEECALENDARID, employees.NAME EMPLOYEE, STARTDATE, ENDDATE, projects.NAME PROJECT, DESCRIPTION
		FROM employeecalendars
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN employees ON EMPLOYEEID = employeecalendars.EMPLOYEE
		";
		$this->params['order'] = "EMPLOYEECALENDARID DESC"; 
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
			'EMPLOYEECALENDARID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'EMPLOYEE' => array(
				'class' => 'sorting',
			),
			'STARTDATE' => array(
				'class' => 'sorting',
				'type' => "datetime",
			),
			'ENDDATE' => array(
				'class' => 'sorting',
				'type' => "datetime",
			),
			'PROJECT' => array(
				'class' => 'sorting',
			),
			'DESCRIPTION' => array(
				'class' => 'sorting',
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'EMPLOYEECALENDARID' => array(
				'class' => 'col-md-3',
				'type' => "primarykey",
				'hidden' => true,
			),
			'EMPLOYEE' => array(
				'class' => 'col-md-6 select2',
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee())
			),
			'STARTDATE' => array(
				'class' => 'col-md-3',
				'type' => "datetime",
			),
			'ENDDATE' => array(
				'class' => 'col-md-3',
				'type' => "datetime",
			),
			'PROJECT' => array(
				'class' => 'col-md-6 select2',
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjects())
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
