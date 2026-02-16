<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_file extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		if( isset($this->urisegments['valpk']) ){
		      $this->session->set_userdata('employee_files_where', $this->uri->uri_to_assoc(4) );
		}
		if( !$this->session->has_userdata("employee_files_where") ){
		      echo "Invalid parameters !";exit;
		}
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		$this->params['name'] = $this->lang->line("employee_file");
		$this->params['maincontent'] = "employee_file";
		$this->params['simpleform'] = true;
		$this->params['table'] = "employeefiles";
		$this->params['sql'] = "SELECT EMPLOYEEFILEID,employees.NAME EMPLOYEE, FILENAME, employeefiles.DESCRIPTION, FILE FROM employeefiles
		LEFT JOIN employees ON EMPLOYEEID = EMPLOYEE
		WHERE 1 
		";
		$this->params['order'] = "EMPLOYEEFILEID DESC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'EMPLOYEEFILEID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'FILENAME' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'FILE' => array(
				'class' => 'sorting',
				'type' => "download",
			),
			'DESCRIPTION' => array(
				'class' => 'sorting',
				'type' => "",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'EMPLOYEEFILEID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'FILENAME' => array(
				'class' => 'col-md-3',
				'type' => "",
			),
			'FILE' => array(
				'class' => 'col-md-3',
				'type' => "file",
			),
			'DESCRIPTION' => array(
				'class' => 'col-md-3',
				'type' => "textarea",
			),
		);
	}
	
	function getData(){
		$where = $this->session->userdata()['employee_files_where']['pk']." = ".$this->session->userdata()['employee_files_where']['valpk'];
		$this->params['sql'] .=  " AND ".$where;
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "Employee : " . $this->Mmasterdata->getEmployeeNameById($this->session->userdata()['employee_files_where']['valpk'])
		);
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "Employee : " . $this->Mmasterdata->getEmployeeNameById($this->session->userdata()['employee_files_where']['valpk'])
		);
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['EMPLOYEE'] = $this->session->userdata()['employee_files_where']['valpk'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "Employee : " . $this->Mmasterdata->getEmployeeNameById($this->session->userdata()['employee_files_where']['valpk'])
		);
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['EMPLOYEE'] = $this->session->userdata()['employee_files_where']['valpk'];
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
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
			loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
		</script>";
	}
	
}
