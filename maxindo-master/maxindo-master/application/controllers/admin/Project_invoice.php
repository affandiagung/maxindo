<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_invoice extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
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
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_invoice";
		$this->params['table'] = "invoices";
		$this->params['sql'] = "SELECT INVOICEID
		FROM invoices
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."' 
		";
		$this->params['query-total'] = "SELECT SUM(FEE) as FEE
		FROM projectmembers WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";
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
			'PROJECTMEMBERID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'EMPLOYEE' => array(
				'class' => "sorting",
			),
			'UNIT' => array(
				'class' => "sorting",
			),
			'JOBPOSITION' => array(
				'class' => "sorting",
			),
			'NOTES' => array(
				'class' => "sorting",
			),
			'FEE' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),	
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTMEMBERID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'EMPLOYEE' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee()),
			),
			'UNIT' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getUnit()),
			),
			'JOBPOSITION' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getJobposition()),
			),
			'FEE' => array(
				'class' => "col-md-3",
				'type' => "number",
				'validation' => 'required',
				'value' => 0
			),			
			'NOTES' => array(
				'class' => "col-md-6",
				'type' => "textarea",
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
			$('#EMPLOYEE').change(function(){
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getEmployee")."';
				let datapost = {
					EMPLOYEEID: $(this).val()
				}
				$.post(target, datapost, function(e){
					$('#JOBPOSITION').val( e.JOBPOSITION ).select2().trigger('change');
					$('#UNIT').val( e.UNIT ).select2().trigger('change');
				},'json');
			});
		</script>";
	}

	function getEmployee(){
		$id = $this->input->post("EMPLOYEEID");
		$emp = $this->db->where("EMPLOYEEID", $id)->get("employees")->row();
		echo json_encode($emp);
	}

	function add(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
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
