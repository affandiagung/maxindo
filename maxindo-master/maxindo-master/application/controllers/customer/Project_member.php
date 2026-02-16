<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_member extends CI_Controller{
	
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
		$this->params['command'] = "browse";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_member";
		$this->params['table'] = "projectmembers";
		$this->params['sql'] = "SELECT PROJECTMEMBERID,
		employees.NAME as EMPLOYEE,
		units.NAME as UNIT,
		jobpositions.NAME as JOBPOSITION,
		NOTES, FEE
		FROM projectmembers
		LEFT JOIN employees ON projectmembers.EMPLOYEE=EMPLOYEEID
		LEFT JOIN units ON projectmembers.UNIT=UNITID
		LEFT JOIN jobpositions ON projectmembers.JOBPOSITION=JOBPOSITIONID
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
	
}
