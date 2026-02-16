<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing_lead extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,project_lead_detail,search,sendnotif";
		$this->params['name'] = $this->lang->line("marketing_lead");
		$this->params['table'] = "projects";
		$this->params['sql'] = "SELECT projects.*,
		employees.NAME ACCOUNT,
		customers.NAME as CUSTOMERNAME,
		customercontacts.NAME as CUSTOMERCONTACTNAME,
		customercontacts.PHONE as CUSTOMERCONTACTPHONE,
		customers.ADDRESS as CUSTOMERADDRESS,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') as PROJECTSTAGENAME,
		projecttypes.NAME as PROJECTTYPENAME
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN customercontacts ON projects.CUSTOMERCONTACT = customercontacts.CUSTOMERCONTACTID
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID
		WHERE 1
		";
		if( $this->logged['PRIVILEGE'] == "MRK") {
			$this->params['sql'] .= " AND EMPLOYEE = '".$this->logged["EMPLOYEE"]."'";
		}
		$this->params['sqlupdate'] = $this->params['sql'];
		$this->params['order'] = "PROJECTID DESC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();

		$this->params['cmd'] = array(
			'project_lead_detail' => array(
				'url' => "javascript:loadpopup(\"".site_url( $this->router->fetch_directory() . "project_lead_detail/index/[urlparams]" ) . "\"".");",
				'icon' => "fa fa-list text-warning",
			),
			'sendnotif' => array(
				'icon' => "fab fa-whatsapp",
				'url' => "javascript:loadcontent(\"engine-content\",\"".site_url("admin/marketing_lead/sendNotif/[urlparams]")."\")"
			)
		);
		$this->params['search'] = array(
			'PROJECTSTAGE' => array(
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectStage(),'-Semua-'),
				'class' => "select2"
			)
		);

		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "loadpopup(\"".site_url( $this->router->fetch_directory() . "project_lead_detail/index/[urlparams]" ) . "\"".");";
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
			'CUSTOMERNAME'=> array(
				'class' => 'sorting',
			),
			'PROJECTLOCATION' => array(
				'class' => 'sorting',
			),
			'PROJECTSTART' => array(
				'class' => 'sorting',
				'type' => "date"
			),
			'PROJECTEND' => array(
				'class' => 'sorting',
				'type' => "date"
			),
			// 'PROJECTDURATION' => array(
			// 	'class' => "sorting",
			// 	'type' => "",
			// 	'rtag' => " Hari",
			// ),
			'ACCOUNT' => array(
				'class' => "sorting",
			),
			'PROJECTORDERCODE' => array(
				'class' => 'sorting',
			),
			'PROJECTSTAGENAME' => array(
				'class' => 'sorting',
				/*'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getProjectstage(),*/
			),
			'WAREHOUSEAPPROVALSTATUS' => array(
				'class' => 'sorting',
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),
			'OPERATIONALAPPROVALSTATUS' => array(
				'class' => 'sorting',
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			)
			// 'NAME' => array(
			// 	'class' => 'sorting',
			// ),
			// 'PROJECTSTAGENAME' => array(
			// 	'class' => 'sorting',
			// ),
			// 'PROJECTTYPENAME' => array(
			// 	'class' => 'sorting',
			// ),
			// 'LEADDATE' => array(
			// 	'class' => "sorting",
			// 	'type' => "date",
			// ),
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
			'PROJECTTYPE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectType()),
			),
			'PROJECTSTAGE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getProjectstage( 4 ),
				'value' => "1",
			),
			'EMPLOYEE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee()),
				'caption' => "Account"
			),
			'CUSTOMER' => array(
				'class' => 'col-md-6 select2',
				'type' => "popup",
				'validation' => "required",
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
			'CUSTOMERCONTACT' => array(
				'class' => 'col-md-6',
				'type' => "popup",
				'popup_url' => site_url( $this->router->fetch_directory() . "customer_contact_popup/index/" ),
			),
			'CUSTOMERCONTACTNAME' => array(
				'class' => "col-md-6",
				'disabled' => true
			),
			'CUSTOMERCONTACTPHONE' => array(
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
			'DEALDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
			),
			'PROJECTSTART' => array(
				'class' => "col-md-6",
				'type' => "date",
				'validation' => "required"
			),
			'PROJECTEND' => array(
				'class' => "col-md-6",
				'type' => "date",
				'validation' => "required"
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'caption' => "Nama Event"
			),
			'SETUPDATE' => array(
				'class' => "col-md-6",
				'type' => "datetime",
			),
			'CLEARDATE' => array(
				'class' => "col-md-6",
				'type' => "datetime",
			),
			'GRDATE' => array(
				'class' => "col-md-6",
				'type' => "datetime",
			),
			'DISPLACEDATE' => array(
				'class' => "col-md-6",
				'type' => "datetime",
			),
			'OTHER' => array(
				'type' => "separator"
			),
			'CONTRACTFILE' => array(
				'class' => "col-md-6",
				'type' => "file",
			),
			'PROJECTNOTES' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'EVENTTYPE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEventtype()),
			),
			/*'QUOTATIONDATE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'DEALDATE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'INVOICEDATE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'PAYMENTDATE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'TOTAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'PAYMENTAMOUNT' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
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
			'QUOTATIONFILE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'INVOICEFILE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'PAYMENTRECEIPTFILE' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),
			'QUOTATIONNOTES' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),*/

		);
		if( $this->logged['PRIVILEGE'] == "MRK"){
			unset($this->params['fieldadd']['EMPLOYEE']);
		}
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
		$this->params['jsinclude'] = "
		<div id='inventory_schedule'></div>
		<script type='text/javascript'>
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

			function setCustomerContact(id, value){
				$('#' + id). val( value );
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getCustomerContact/")."';
				let datapost = {
					id: value
				}
				$.post(target, datapost, function(e){
					$('#CUSTOMERCONTACTNAME').val( e.NAME );
					$('#CUSTOMERCONTACTPHONE').val( e.PHONE );
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
		$this->session->set_userdata("customerId", $customer->CUSTOMERID);
		echo json_encode( $customer );
	}

	function getCustomerContact(){
		$id = $this->input->post("id");
		$customercontact = $this->db->get_where("customercontacts", array("CUSTOMERCONTACTID" => $id))->row();
		echo json_encode( $customercontact );
	}

	function add(){
		if( count($_POST) > 0 ){
			if( $this->logged['PRIVILEGE'] == "MRK"){
				$_POST["EMPLOYEE"] = $this->logged["EMPLOYEE"];
			}
			// $employee = $this->db->where("EMPLOYEEID", $_POST['EMPLOYEE'])->get("employees")->row();
			// $message = $employee->NAME . " ada lead dengan kode '".$_POST['PROJECTORDERCODE']."' pada tanggal " . date_to_ID($_POST['PROJECTSTART']) . " s/d " . date_to_ID($_POST['PROJECTEND']) . ". Mohon untuk validasi";
			// $this->Mmasterdata->sendWA( $this->Mmasterdata->getConfigItem("OPERATIONALNUMBER"), $message);
			// $this->Mmasterdata->sendWA( $this->Mmasterdata->getConfigItem("WAREHOUSENUMBER"), $message);
		}
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		// Set Customer
		if( isset($this->urisegments['valpk'])){
			$project = $this->db->where("PROJECTID", $this->urisegments['valpk'])->get("projects")->row();
			if ($project->PROJECTSTAGE == "4") {
				$this->params['showcontrol'] = FALSE;
				$this->params['disabled'] = TRUE;
			}
			if( $project->CUSTOMER != "" ){
				$this->session->set_userdata("customerId", $project->CUSTOMER);
			}
		}
		if (count($_POST)>0) {
			if ($_POST['PROJECTSTAGE'] == "4") { # deal -> insert ke jadwal
				$pj_quotation = $this->Mmasterdata->getProjectQuotation($_POST['PROJECTID']);
				$schedinsert = array();
				foreach ($pj_quotation as $key => $value) {

					$sched = array();
					$project = $this->Mmasterdata->getProject($value['PROJECT'])['0'];
					$sched['INVENTORY'] = $value['INVENTORY'];
					$sched['STARTDATE'] = $project['SETUPDATE'];
					$sched['ENDDATE'] = $project['CLEARDATE'];
					$sched['PROJECT'] = $value['PROJECT'];
					$sched['USEDCOUNT'] = $value['QTY'];
					$sched['DESCRIPTION'] = $value['DESCRIPTION'];
					$schedinsert[$key] = $sched;
					// $this->db->insert('inventorycalendars',$sched);
				}
				if (!empty($schedinsert)) {
					$this->db->insert_batch("inventorycalendars",$schedinsert);
				}
			}
		}
		$this->jsinclude();
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			$this->db->where('PROJECT',$this->urisegments['valpk'])->delete('inventorycalendars');
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

	function sendNotif(){
		$projectID = $this->urisegments['valpk'];
		$project = $this->Mmasterdata->getProjectDetail( $projectID );
		$message = $project->EMPLOYEENAME . " ada lead dengan kode '".$project->PROJECTORDERCODE."' pada tanggal " . date_to_ID($project->PROJECTSTART) . " s/d " . date_to_ID($project->PROJECTEND) . ". Mohon untuk disetujui atau tidak";
		$this->Mmasterdata->sendWA( $this->Mmasterdata->getConfigItem("OPERATIONALNUMBER"), $message);
		$this->Mmasterdata->sendWA( $this->Mmasterdata->getConfigItem("WAREHOUSENUMBER"), $message);
		echo "<script>
		swal.fire({
      icon: 'success',
      title: 'Notifikasi WA Berhasil Dikirimkan ke Admin Gudang dan Admin Operasional',
      showConfirmButton: !1,
      timer: 1500
    }).then(function(){
			loadcontent('engine-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
    });
		</script>";
	}
	
}
