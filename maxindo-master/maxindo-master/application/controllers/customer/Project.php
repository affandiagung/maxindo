<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller{
	
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
		$this->params['command'] = "browse,projectdetail,search";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "projects";
		$this->params['sql'] = "SELECT projects.*,
		employees.NAME EMPLOYEENAME,
		customers.NAME as CUSTOMERNAME,
		customers.ADDRESS as CUSTOMERADDRESS,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') as PROJECTSTAGENAME,
		projecttypes.NAME as PROJECTTYPENAME
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID
		WHERE 1 AND CUSTOMER = '".$this->logged['CUSTOMER']."'";
		$this->params['sqlupdate'] = $this->params['sql'];
		$this->params['order'] = "PROJECTID DESC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "project_detail/index/[urlparams]" ) . "\"".");";
		$this->params['cmd'] = array(
			'projectdetail' => array(
				'url' => "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "project_detail/index/[urlparams]" ) . "\"".");",
				'icon' => "fa fa-list text-danger",
			),
		);
		$this->params['search'] = array(
			'PROJECTSTAGE' => array(
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectStage(),'-Semua-'),
				'class' => "select2"
			)
		);
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'PROJECTORDERCODE' => array(
				'class' => 'sorting',
			),
			'NAME' => array(
				'class' => 'sorting',
			),
			'PROJECTSTAGENAME' => array(
				'class' => 'sorting',
			),
			'PROJECTTYPENAME' => array(
				'class' => 'sorting',
			),
			'EMPLOYEENAME' => array(
				'class' => "sorting",
				'type' => "",
			),
			'CUSTOMERNAME'=> array(
				'class' => 'sorting',
				'type' => "",
			),
			'PROJECTSTART' => array(
				'class' => "sorting",
				'type' => "date",
			),
			'PROJECTEND' => array(
				'class' => "sorting",
				'type' => "date",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'PROJECTORDERCODE' => array(
				'class' => 'col-md-6',
				'validation' => "required",
				'button' => "<i class='fa fa-sync-alt'></i>",
				'value' => $this->Mmasterdata->getProjectOrderCode()
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'validation' => "required",
			),
			'PROJECTTYPE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectType()),
			),
			'PROJECTSTAGE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getProjectstage(),
				'value' => "1",
			),
			'EMPLOYEE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee()),
			),
			'CUSTOMER' => array(
				'class' => 'col-md-6 select2',
				'type' => "popup",
				'popup_url' => site_url( $this->router->fetch_directory() . "customer_popup/index/" ),
			),
			'CUSTOMERNAME' => array(
				'class' => "col-md-6",
				'disabled' => true
			),
			'CUSTOMERADDRESS' => array(
				'class' => "col-md-6",
				'type' => "textarea",
				'disabled' => true
			),
			'TIMELOCATION' => array(
				'type' => "separator"
			),
			'PROJECTLOCATION' => array(
				'class' => "col-md-6",
			),
			'PROJECTCOORDINATE' => array(
				'class' => "col-md-6",
				'type' => "map",
				'address_id' => "PROJECTLOCATION"
			),
			'LEADDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'PROJECTSTART' => array(
				'class' => "col-md-6",
				'type' => "datetime",
			),
			'PROJECTEND' => array(
				'class' => "col-md-6",
				'type' => "datetime",
			),
			// 'QUOTATIONDATE' => array(
			// 	'class' => "col-md-6",
			// 	'type' => "date",
			// ),
			'DEALDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
			),
			'TOTAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'PAYMENTAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			/*
			'TOTRENTAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'TOTSERVICEAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'TOTSELLAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'CREATEDATE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'CREATEBY' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			*/
			'CONTRACTFILE' => array(
				'class' => "col-md-6",
				'type' => "file",
			),
			/*'QUOTATIONNOTES' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),*/
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		if( count($_POST) > 0 ){
			if( $_POST["PROJECTSTAGE"] != "" ){
				$_POST['p_search'] = " AND PROJECTSTAGE='".$_POST["PROJECTSTAGE"]."'";
			}
		}
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			function setCustomer(id, value){
				$('#' + id). val( value );
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getCustomer/")."';
				let datapost = {
					id: value
				}
				$.post(target, datapost, function(e){
					$('#CUSTOMERNAME').val( e.NAME );
					$('#CUSTOMERADDRESS').val( e.ADDRESS );
				},'json');
				$('[data-bs-dismiss=\"modal\"]').click();
			}

			$('#btn-PROJECTORDERCODE').click(function(){
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getOrderCode/")."';
				let datapost = [];
				$.post(target, datapost, function(e){
					$('#PROJECTORDERCODE').val( e );
				});
			});
		</script>";
	}

	function getOrderCode(){
		echo $this->Mmasterdata->getProjectOrderCode();
	}

	function getCustomer(){
		$id = $this->input->post("id");
		$customer = $this->db->get_where("customers", array("CUSTOMERID" => $id))->row();
		echo json_encode( $customer );
	}



	
}
