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
		$this->params['command'] = "browse,add,delete,deleteall,search";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project";
		$this->params['table'] = "projects";
		$this->params['sql'] = "SELECT 
		projects.*,
		employees.NAME ACCOUNT,
		employees.EMPLOYEEID,
		CONCAT(customers.NAME, IF( projects.DRYRENT = 1,'<br /><span class=\"badge badge-danger\">DRY RENT</span>', '')) AS CUSTOMERNAME,
		customers.ADDRESS AS CUSTOMERADDRESS,
		customercontacts.NAME as CUSTOMERCONTACTNAME,
		customercontacts.PHONE as CUSTOMERCONTACTPHONE,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') AS PROJECTSTAGENAME,
		projecttypes.NAME AS PROJECTTYPENAME,
		'' as PROGRESS,
		IF(DISPLACEDATE < CURRENT_DATE(),1,0) as SELESAI
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN customercontacts ON projects.CUSTOMERCONTACT = customercontacts.CUSTOMERCONTACTID
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID";

		if( $this->logged['PRIVILEGE'] == "EMP"){
			$this->params['command'] = "browse,projectdetail,search";
			$this->params['sql'] .= " 
			LEFT JOIN projectmembers ON projectmembers.PROJECT=projects.PROJECTID
			WHERE projectmembers.EMPLOYEE='".$this->logged['EMPLOYEE']."'
			GROUP BY PROJECTID";
		} else {
			$this->params['sql'] .= " WHERE 1";
		}
		$this->params['sqlupdate'] = $this->params['sql'];
		$this->params['order'] = "SELESAI ASC, SETUPDATE DESC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
		$this->params['search'] = array(
			'PROJECTSTAGE' => array(
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectStage(),'-Semua-'),
				'class' => "select2"
			)
		);
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "loadpopup(\"".site_url( $this->router->fetch_directory() . "project_detail/index/[urlparams]" ) . "\"".");";
		if( $this->logged['PRIVILEGE'] == "MRK" ){
			$this->params['customrowcallback'] = "
				if( data.EMPLOYEEID != '".$this->logged['EMPLOYEE']."'){
					$(row).find('.btn-delete').remove();
				}
				if( data.PROJECTSTAGE >= 4 ){
					$(row).find('.btn-delete').remove();
				}

				if( data.SELESAI == 0){
					$(row).find('th').addClass('table-warning');
				}
			";
		}
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
			// 'NAME'=> array(
			// 	'title' => "Nama Event",
			// 	'class' => 'sorting',
			// ),
			'CUSTOMERNAME'=> array(
				'class' => 'sorting',
			),
			'PROJECTLOCATION' => array(
				'class' => 'sorting',
				'type' => "function",
				'func' => "splitLokasi",
				'model' => "Mmasterdata",
				'params' => "PROJECTLOCATION"
			),
			'SETUPDATE' => array(
				'class' => 'sorting',
				'type' => "datetime"
			),
			'DISPLACEDATE' => array(
				'class' => 'sorting',
				'type' => "datetime"
			),
			'PROJECTDURATION' => array(
				'class' => "sorting",
				'type' => "",
				'rtag' => " Hari",
			),
			'ACCOUNT' => array(
				'class' => "sorting",
			),
			'PROJECTORDERCODE' => array(
				'class' => 'sorting',
			),
			'PROJECTSTAGENAME' => array(
				'class' => 'sorting',
			),
			'PROJECTSTAGE' => array(
				'hidden' => true,
			),
			'EMPLOYEEID' => array(
				'hidden' => true
			),
			'APPROVALSTATUS' => array(
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getApprovalStatus(),
			),
			'PROGRESS' => array(
				'class' => 'sorting',
				'type' => "function",
				'model' => "Mmasterdata",
				'func' => "getProjectProgress",
				'width' => "150px"
			)
		);

		if( $this->logged['PRIVILEGE'] == "EMP" ){
			unset($this->params['fieldselect']['CUSTOMERNAME']);
			unset($this->params['fieldselect']['ACCOUNT']);
			unset($this->params['fieldselect']['PROJECTORDERCODE']);
		}
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
				'sourcequery' => $this->Mmasterdata->getProjectstage(),
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
				'validation' => "required",
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
			'DRYRENT' => array(
				'class' => "col-md-6",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
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
			'PROJECTROOM' => array(
				'class' => "col-md-6",
			),
			'ELECTRICITYSOURCE' => array(
				'class' => "col-md-6",
			),
			'AREASIZE' => array(
				'class' => "col-md-6",
			),
			'LEADDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'QUOTATION' => array(
				'type' => "separator"
			),
			'QUOTATIONDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'QUOTATIONENDDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'value' => date('Y-m-d',strtotime(date('Y-m-d').' + 4 days')),
			),
			'RENTALDETAIL' => array(
				'type' => "separator"
			),
			'PROJECTSTART' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'PROJECTEND' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'caption' => "Nama Event"
			),
			'SETUPDATE' => array(
				'class' => "col-md-3",
				'type' => "datetime",
				'validation' => "required"
			),
			'CLEARDATE' => array(
				'class' => "col-md-3",
				'type' => "datetime",
			),
			'GRDATE' => array(
				'class' => "col-md-3",
				'type' => "datetime",
			),
			'GRVALUE' => array(
				'class' => "col-md-3",
				'type' => "number",
			),
			'DISPLACEDATE' => array(
				'class' => "col-md-3",
				'type' => "datetime",
				'validation' => "required"
			),
			'DEALDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'TOTAMOUNT' => array(
				'class' => "col-md-3",
				'type' => "number",
			),
			'PAYMENTAMOUNT' => array(
				'class' => "col-md-3",
				'type' => "number",
			),
			// 'TAXPERCENT' => array(
			// 	'class' => "col-md-3",
			// 	'type' => "decimal",
			// 	'help' => "%",
			// ),
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
			'PICPPERSON' => array(
				'type' => "separator"
			),
			'CLIENTPICNAME1' => array(
				'class' => "col-md-6",
				// 'validation' => "required",
			),
			'CLIENTPICNUMBER1' => array(
				'class' => "col-md-6",
				// 'validation' => "required",
			),
			'CLIENTPICNAME2' => array(
				'class' => "col-md-6",
			),
			'CLIENTPICNUMBER2' => array(
				'class' => "col-md-6",
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
			'APPROVAL' => array(
				'type' => 'separator'
			),
			/*'WAREHOUSEAPPROVALSTATUS' => array(
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
				'class' => "col-md-6 select2",
			),
			'WAREHOUSEAPPROVALDATE' => array(
				'type' => "datetime",
				'class' => "col-md-3",
			),
			'WAREHOUSEREJECTMESSAGE' => array(
				'type' => "textarea",
				'class' => "col-md-6",
			),*/
			'OPERATIONALAPPROVALSTATUS' => array(
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
				'class' => "col-md-6 select2",
			),
			'OPERATIONALAPPROVALDATE' => array(
				'type' => "datetime",
				'class' => "col-md-3",
			),
			'OPERATIONALREJECTMESSAGE' => array(
				'type' => "textarea",
				'class' => "col-md-6",
			),
			'APPROVALSTATUS' => array(
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getApprovalStatus(),
				'class' => "col-md-6 select2",
			),
			'REJECTREASON' => array(
				'type' => "textarea",
				'class' => "col-md-6",
			),
			/*'QUOTATIONNOTES' => array(
				'class' => "col-md-6",
				'type' => "",
				'hidden' => "",
			),*/
			/*'FINISH' => array(
				'class' => "col-md-6",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),*/
		);
		if( $this->logged['PRIVILEGE'] == "MRK" ){
			$this->params['fieldadd']['OPERATIONALAPPROVALSTATUS']['disabled'] = true;
			$this->params['fieldadd']['OPERATIONALAPPROVALDATE']['disabled'] = true;
			$this->params['fieldadd']['OPERATIONALREJECTMESSAGE']['disabled'] = true;
			// $this->params['fieldadd']['FINISH']['disabled'] = true;
			$this->params['fieldadd']['EMPLOYEE']['hidden'] = true;
		}
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		if( count($_POST) > 0 ){
			if( isset($_POST["PROJECTSTAGE"]) && $_POST["PROJECTSTAGE"] != "" ){
				$_POST['p_search'] = " AND PROJECTSTAGE='".$_POST["PROJECTSTAGE"]."'";
			}
		}
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			$('#modalPopupContainer #project_master_tab .card-toolbar').hide();
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
				$('#inputModal [data-bs-dismiss=\"modal\"]').click();
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
				$('#inputModal [data-bs-dismiss=\"modal\"]').click();
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
		$this->params['maincontent'] = "project_master";
		if( !empty($_POST) ){
			$_POST['CREATEBY'] = $this->logged['USERID'];
			if( $this->logged['PRIVILEGE'] == "MRK" ){
				$_POST['EMPLOYEE'] = $this->logged['EMPLOYEE'];
			}
			if ($_POST['PROJECTSTAGE'] == "4") {
				$this->params['alert'] = array(
					'type' => "danger",
					'message' => "Quotation belum di approve oleh marketing manager!",
				);
				$_POST=array();
			}
		}

		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->params['maincontent'] = "project_master";
		// Set Customer
		if( isset($this->urisegments['valpk'])){
			$project = $this->db->where("PROJECTID", $this->urisegments['valpk'])->get("projects")->row();
			if( $project->CUSTOMER != "" ){
				$this->session->set_userdata("customerId", $project->CUSTOMER);
			}
			if( $this->logged['PRIVILEGE'] == "MRK" ){
				if( $this->logged['EMPLOYEE'] != $project->EMPLOYEE ) {
					$this->params['disabled'] = true;
					$this->params['showcontrol'] = false;
				}
				if( $project->PROJECTSTAGE >= 4 ){
					$this->params['fieldadd']['PROJECTSTAGE']['disabled'] = true;
				}
			}
		}
		if( !empty($_POST) ){
			$_POST['UPDATEBY'] = $this->logged['USERID'];
			if( $_POST['PROJECTSTAGE'] >= 4 ){
				if ($_POST["APPROVALSTATUS"] != "1") {
					$_POST=array();
					$this->params['alert'] = array(
						'type' => "danger",
						'message' => "Quotation belum di approve oleh marketing manager!",
					);
				}
			}
			if( $this->logged['PRIVILEGE'] == "MRK" ){
				if( $project->PROJECTSTAGE >= 4 ){
					unset( $_POST['PROJECTSTAGE']);
				}
			}
		}
		$this->jsinclude();
		$this->params['wizard'] = true;
		$this->load->library("engine",$this->params);
		$edit =  $this->engine->edit();
		if( $edit == 1){
			$project_id = $this->urisegments['valpk'];
			$duration = $this->db->where('PROJECTID',$project_id)->get('projects')->row()->PROJECTDURATION;
			$this->db->where('PROJECT',$project_id)->set('DURATION',$duration)->update('projectquotations');
			echo "<script>
			swal.fire({
					icon: 'success',
					title: 'Berhasil',
					showConfirmButton: !1,
					timer: 1500
				});
			loadcontent('project-content','".site_url($this->router->fetch_directory().$this->router->fetch_class() . "/browse/")."');
			loadcontent('project_master-content','".site_url($this->router->fetch_directory().$this->router->fetch_class() . "/edit/pk/".$this->urisegments['pk']."/valpk/".$this->urisegments['valpk']."/")."');
			</script>";
		} else {
			echo $edit;
		}
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

	function filter( $field, $value ){
		if( isset($field) && isset($value) ){
			$_POST['p_search'] = " AND ".$this->params['table'].".".$field." = ".$this->db->escape($value)."";
		}
		$this->browse();
	}
	
}
