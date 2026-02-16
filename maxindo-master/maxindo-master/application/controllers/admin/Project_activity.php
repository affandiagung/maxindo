<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_activity extends CI_Controller{
	
	private $params = array();
	private $logged = array();
	private $projectname = "";

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
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_activity";
		$this->params['table'] = "projectactivities";
		$this->params['sql'] = "SELECT PROJECTACTIVITYID,
		employees.NAME as EMPLOYEE, 
		ACTIVITY, 
		activitytypes.NAME AS ACTIVITYTYPE, 
		ACTIVITYDATE,
		projectactivities.CREATEBY, projectactivities.UPDATEBY, projectactivities.CREATEAT, projectactivities.UPDATEAT
		FROM projectactivities
		LEFT JOIN employees ON projectactivities.EMPLOYEE=EMPLOYEEID
		LEFT JOIN employees employees_a ON projectactivities.PROJECTMEMBER=employees_a.EMPLOYEEID
		LEFT JOIN activitytypes ON projectactivities.ACTIVITYTYPE = activitytypes.ACTIVITYTYPEID
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";

		if( $this->logged['PRIVILEGE'] == "EMP"){
			$this->params['sql'] .= " AND projectactivities.EMPLOYEE = '".$this->logged['EMPLOYEE']."'";
			$this->params['command'] = "browse,add";
		}

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
			'PROJECTACTIVITYID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			/*'EMPLOYEE' => array(
				'class' => "sorting",
			),*/
			'ACTIVITYDATE' => array(
				'class' => "sorting",
				'type' => "datetime",
				'width' => "150px"
			),		
			'EMPLOYEE' => array(
				'class' => "sorting",
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
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTACTIVITYID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'ACTIVITYDATE' => array(
				'type' => "datetime",
				'validation' => "required",
				'class' => "col-md-3",
				'value' => date("Y-m-d H:i:s")
			),
			'PROJECTMEMBER' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectMember( $this->session->userdata("PROJECTID") )),
			),
			'ACTIVITYTYPE' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getActivityType()),
			),
			'ACTIVITY' => array(
				'class' => "col-md-6",
				'type' => "textarea",
				'validation' => 'required',
			),
			'CREATEBY' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'CREATEAT' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'UPDATEBY' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'UPDATEAT' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
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
			
		</script>";
	}

	function add(){
		$this->jsinclude();
		$this->getfieldedit();
		if (!empty($_POST) && $_POST['PROJECTMEMBER'] != ""){
			$_POST['EMPLOYEE'] = $this->Mmasterdata->getEmployeeIdByMember( $_POST['PROJECTMEMBER'] );
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->getfieldedit();
		if ( !empty($_POST) && $_POST['PROJECTMEMBER'] != "" ){
			$_POST['EMPLOYEE'] = $this->Mmasterdata->getEmployeeIdByMember( $_POST['PROJECTMEMBER'] );
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['UPDATEBY'] = $this->logged['USERID'];
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

	function quick_action(){
		if( $this->logged['PRIVILEGE'] == "EMP" ){
			echo "
				<button type='button' id='start_setup' class='btn btn-success btn-quick-action'>Start Setup</button>
				<button type='button' id='selesai_setup' class='btn btn-warning btn-quick-action'>Selesai Setup</button>
				<button type='button' id='start_event' class='btn btn-info btn-quick-action'>Start Event</button>
				<button type='button' id='selesai_event' class='btn btn-primary btn-quick-action'>Selesai Event</button>
				<button type='button' id='selesai_bongkar' class='btn btn-danger btn-quick-action'>Selesai Bongkar</button>
			";

			echo "<script>
				$('.btn-quick-action').click(function(){
					let target = site_url + 'admin/project_activity/submit_activity';
					let datapost = {
						id: $(this).attr('id')
					}
					$.post(target, datapost, function(e){
						loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
				});
			</script>";
		}
	}

	function submit_activity(){
		$employee = $this->logged['EMPLOYEE'];
		$projectMember = $this->db->where("EMPLOYEE", $employee)->where("PROJECT", $this->session->userdata("PROJECTID"))->get("projectmembers")->row();
		$activity = $this->input->post("id");
		$type = "";
		switch($activity){
			case "start_setup":
				$type = 7;
				break;
			case "selesai_setup":
				$type = 7;
				break;
			case "start_event":
				$type = 10;
				break;
			case "selesai_event":
				$type = 11;
				break;
			case "selesai_bongkar":
				$type = 12;
				break;
		}
		$activity = ucwords( strtolower(implode(" ",explode("_",$activity))) );
		$this->db->insert( "projectactivities", array(
			"PROJECT" => $this->session->userdata("PROJECTID"),
			"EMPLOYEE" => $this->logged['EMPLOYEE'],
			"PROJECTMEMBER" => $projectMember->PROJECTMEMBERID,
			"ACTIVITY" => $activity,
			"ACTIVITYTYPE" => $type,
			"ACTIVITYDATE" => date("Y-m-d H:i:s")
		));
	}
	
}
