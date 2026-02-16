<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_activity extends CI_Controller{
	
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
		$this->params['maincontent'] = "project_activity";
		$this->params['table'] = "projectactivities";
		$this->params['sql'] = "SELECT PROJECTACTIVITYID,
		employees.NAME as EMPLOYEE, ACTIVITY, activitytypes.NAME AS ACTIVITYTYPE, ACTIVITYDATE
		FROM projectactivities
		LEFT JOIN employees ON projectactivities.EMPLOYEE=EMPLOYEEID
		LEFT JOIN employees employees_a ON projectactivities.PROJECTMEMBER=employees_a.EMPLOYEEID
		LEFT JOIN activitytypes ON projectactivities.ACTIVITYTYPE = activitytypes.ACTIVITYTYPEID
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."' 
		";
		
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTACTIVITYID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'ACTIVITYDATE' => array(
				'class' => "sorting",
				'type' => "datetime",
				'width' => "150px"
			),	
			'ACTIVITYTYPE' => array(
				'class' => "sorting",
				'width' => "150px"
			),
			'ACTIVITY' => array(
				'class' => "sorting",
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
