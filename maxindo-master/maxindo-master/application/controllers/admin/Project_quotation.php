<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_quotation extends CI_Controller{
	
	private $params = array();
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,print_head";
		if( $this->logged['PRIVILEGE'] == "MRK" ){
			$project = $this->session->userdata("project");
			if( $this->logged['EMPLOYEE'] != $project->EMPLOYEE ){
				$this->params['command'] = "browse";
			}
		}
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_quotation";
		$this->params['table'] = "projectquotations";
		$this->params['sql'] = "SELECT PROJECTQUOTATIONID,
		projects.NAME PROJECT, 
		inventories.NAME ITEM, 
		projectquotations.QTY, 
		projectquotations.AVAILABLEQTY,
		projectquotations.LACKQTY,
		DURATION as PROJECTDURATION,
		FINALCOST, COST, DISCOUNT, DESCRIPTION SPECIFICATION, SQM, COSTSQM,
		STATUS,ORDERSEQ, TOTALCOST,
		projectquotations.CREATEBY, projectquotations.UPDATEBY, projectquotations.CREATEAT, projectquotations.UPDATEAT,
		GROUP_CONCAT(CONCAT('- ',vendors.NAME,' (No. PO :', SUBRENTNUMBER,') | ', projectsubrentdetails.QTY) ORDER BY SUBRENTNUMBER SEPARATOR '<br />') as VENDOR
		FROM projectquotations
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN inventories ON INVENTORYID = INVENTORY
		LEFT JOIN unittypes ON UNITTYPEID = UNITTYPE
		LEFT JOIN projectsubrentdetails ON projectsubrentdetails.PROJECTQUOTATION=projectquotations.PROJECTQUOTATIONID
		LEFT JOIN projectsubrents ON projectsubrents.PROJECTSUBRENTID=projectsubrentdetails.PROJECTSUBRENT
		LEFT JOIN vendors ON projectsubrents.VENDOR=VENDORID
		WHERE projectquotations.PROJECT='".$this->session->userdata("PROJECTID")."' 
		GROUP BY PROJECTQUOTATIONID
		";
		$this->params['query-total'] = "SELECT SUM(COST) as COST, SUM(DISCOUNT) as DISCOUNT, SUM(FINALCOST) as FINALCOST, SUM(FINALCOST+DISCOUNT) TOTAL, SUM(TOTALCOST) TOTALCOST
		FROM projectquotations WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";

		$this->params['dataTableParam'] = "
			rowReorder: true,
		";

		$this->params['order'] = "ORDERSEQ";
		$this->params['cmd'] = array(
			'print_head' => array(
				'url' => "javascript:loadcontent(\"project_quotation-content\",\"".site_url("admin/Project_quotation/print/")."\")",
				'icon' => "fa fa-print",
			),
		);
		if( $this->logged['PRIVILEGE'] == "MRK" || $this->logged['PRIVILEGE'] == "ADM" ){
			$this->params['command'] .= ",sendnotif_head";
			$this->params['cmd']['sendnotif_head'] = array(
				'icon' => "fab fa-whatsapp",
				'url' => "javascript:loadcontent(\"project_quotation-content\",\"".site_url("admin/Project_quotation/sendNotif/")."\")"
			);
			$projectID = $this->session->userdata("PROJECTID");
			$project = $this->Mmasterdata->getProjectDetail( $projectID );
			$approvalstatus = $project->APPROVALSTATUS;
			if ($approvalstatus==1) {
				$this->params['command'] .= ",sendnotifclient_head";
				$this->params['cmd']['sendnotifclient_head'] = array(
					'icon' => "fab fa-whatsapp text-warning",
					'url' => "javascript:loadcontent(\"project_quotation-content\",\"".site_url("admin/Project_quotation/sendnotifclient/")."\")"
				);
			}
		}
		if( $this->logged['PRIVILEGE'] == "MM" || $this->logged['PRIVILEGE'] == "ADM" ){
			if ($this->logged['PRIVILEGE'] == "mm") {
				unset($this->params['cmd']['sendnotif_head']);
			}
			$this->params['command'] .= ",approve_head,reject_head";
			$this->params['cmd']['approve_head'] = array(
				'icon' => "far fa-check-circle",
				'url' => "javascript:loadcontent(\"project_quotation-content\",\"".site_url("admin/Project_quotation/approve/")."\")"
			);
			$this->params['cmd']['reject_head'] = array(
				'icon' => "far fa-minus-square",
				'url' => "javascript:loadcontent(\"project_quotation-content\",\"".site_url("admin/Project_quotation/reject/")."\")"
			);
		}
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "loadcontent(\"".$this->params['maincontent']."\", \"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/edit/[urlparams]")."\")";
		$this->params['customrowcallback'] = "
			if( data.LACKQTY > 0 ){
				$(row).find('td').addClass('table-danger');
			}
		";
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTQUOTATIONID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			/*'ORDERSEQ' => array(
				'class' => "sorting",
			),*/
			'ITEM' => array(
				'class' => "sorting",
			),
			'SPECIFICATION' => array(
				'class' => "sorting",
			),
			'VENDOR' => array(
				'class' => "sorting",
			),
			'AVAILABLEQTY' => array(
				'class' => "sorting",
			),
			'LACKQTY' => array(
				'class' => "sorting",
			),
			'SQM' => array(
				'class' => "sorting",
			),
			'COSTSQM' => array(
				'class' => "sorting",
				'type' => "number",
				'sumtitle' => true
			),
			'QTY' => array(
				'class' => "sorting",
			),
			'COST' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),
			'PROJECTDURATION' => array(
				'class' => "sorting",
				'decimalplace' => 1
			),
			'TOTALCOST' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),
			'DISCOUNT' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),	
			'FINALCOST' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTQUOTATIONID' => array(
				'class' => "col-md-6",
				'type' => "primarykey",
				'hidden' => true,
			),
			'INVENTORY' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventory()),
			),
			/*'SPECIFICATION' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),*/
			/*'INVENTORYGROUP' => array(
				'class' => "col-md-6",
				'type' => "",
			),*/
			'DESCRIPTION' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'PENGHITUNGANSQM' => array(
				'type' => "separator"
			),
			'AVAILABLESQM' => array(
				'class' => "col-md-3",
				'type' => "decimal",
				'help' => "m<sup>2"
			),
			'SQM' => array(
				'class' => "col-md-3",
				'type' => "decimal",
				'help' => "m<sup>2"
			),
			'COSTSQM' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'PENGHITUNGANUNIT' => array(
				'type' => "separator"
			),
			'AVAILABLEQTY' => array(
				'class' => "col-md-3",
				'type' => "number",
			),			
			'QTY' => array(
				'class' => "col-md-3",
				'type' => "number",
			),
			'LACKQTY' => array(
				'class' => "col-md-3",
				'type' => "number",
			),		
			'COST' => array(
				'class' => "col-md-6",
				'type' => "number",
				'value' => "0",
			),
			'DURATION' => array(
				'class' => "col-md-3",
				'type' => "decimal",
				'decimalplace' => 1,
				// 'readonly' => true,
				'help' => "hari",
				'value' => $this->Mmasterdata->getProjectDuration( $this->session->userdata("PROJECTID") )
			),
			'TOTALCOST' => array(
				'class' => "col-md-3",
				'type' => "number",
				'readonly' => true
			),
			'PENGHITUNGANDISKON' => array(
				'type' => "separator"
			),
			'DISCOUNTPERCENT' => array(
				'class' => "col-md-3",
				'type' => "decimal",
				'value' => "0",
				'help' => "%",
			),				
			'DISCOUNT' => array(
				'class' => "col-md-6",
				'type' => "number",
				'value' => "0",
			),
			'PENGHITUNGANFINAL' => array(
				'type' => "separator"
			),
			'FINALCOST' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'LOG' => array(
				'type' => "separator"
			),
			'CREATEBY' => array(
				'class' => "col-md-6",
				'disabled' => true
			),
			'CREATEAT' => array(
				'class' => "col-md-6",
				'disabled' => true
			),
			'UPDATEBY' => array(
				'class' => "col-md-6",
				'disabled' => true
			),
			'UPDATEAT' => array(
				'class' => "col-md-6",
				'disabled' => true
			)

		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}

	function browse(){
		$this->jsinclude();
		// Hitung Ulang Kesiapan
		$this->Mmasterdata->hitungUlangStock( $this->session->userdata("PROJECTID") );
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function getInventoryCalendar(){
		$inv = $this->input->post("INVENTORYID");
		$project = $this->db->where("PROJECTID", $this->session->userdata("PROJECTID"))->get("projects")->row();
		$startDate = $project->SETUPDATE;
		$endDate = $project->DISPLACEDATE;
		$usedInventory = $this->db->select("IFNULL(SUM(USEDCOUNT),0) as USEDCOUNT")->where("
	    INVENTORY = '".$inv."' AND (
		    ('".$startDate."' <= STARTDATE AND '".$endDate."' <= ENDDATE AND '".$startDate."' <= ENDDATE) OR
		    ('".$startDate."' <= STARTDATE AND '".$endDate."' >= ENDDATE) OR
		    ('".$startDate."' >= STARTDATE AND '".$endDate."' >= ENDDATE AND '".$startDate."' <= ENDDATE) OR
		    ('".$startDate."' >= STARTDATE AND '".$endDate."' <= ENDDATE)
	    )
		")->get("inventorycalendars");
		$used = 0;
		if( $usedInventory->num_rows() > 0 ){
			$row = $usedInventory->row();
			$used = $row->USEDCOUNT;
		}
		$inventory = $this->db->where("INVENTORYID", $inv)->get("inventories")->row();
		$total = $inventory->TOTITEM;
		$avail = $total-$used;
		$avail_sqm = $avail/4;
		// echo $avail." / ".$avail_sqm." sqm";
		echo $avail;
	}

	function reorder(){
		$post = $this->input->post();
		if( isset($post['id']) && isset($post['orderseq']) ){
			$this->db->update("projectquotations", array("ORDERSEQ" => $post['orderseq']), array("PROJECTQUOTATIONID" => $post['id']));
		}
	}

	function jsinclude(){
		$this->params['jsinclude'] = "
		<div id='availability_calendar'></div>
		<script type='text/javascript'>
			tbl_".str_replace("-","_",$this->params['maincontent'])."_content.on( 'row-reorder', function ( e, diff, edit ) {
        $.each(diff, function( index, reorder ){
        	// console.log(reorder);
        	var id = $(reorder.node).attr('id');
        	var ids = id.split('--');
        	var realId = ids[2];
        	var target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/reorder/")."';
        	var datapost = {
        		id: realId,
        		orderseq: reorder.newPosition
        	}
        	$.post(target, datapost);
        });
	    });
			$('#DISCOUNT').blur(function(){
				let cost  =  eval( $('#TOTALCOST').val() );
				let discount  =  eval( $('#DISCOUNT').val() );
				$('#FINALCOST').val(cost-discount);
			});
			$('#COST').blur(function(){
				let cost  =  eval( $('#COST').val() ) * eval( $('#QTY').val() ) * eval( $('#DURATION').val() );
				$('#TOTALCOST').val(cost);
				$('#FINALCOST').val ( eval( $('#TOTALCOST').val() ) - eval( $('#DISCOUNT').val() ) );
				if( $('#SQM').val() ){
					$('#COSTSQM').val( eval( $('#COST').val() ) * 4 );
				}
			});
			$('#DISCOUNTPERCENT').blur(function(){
				let cost  =  eval( $('#TOTALCOST').val() );
				let num = parseFloat(cost);
				let discountpercent = eval( $(this).val() ) / 100;
				let val = num * discountpercent;
				$('#DISCOUNT').val(val).blur();
			});
			$('#SQM').blur(function(){
				$('#QTY').val ( eval( $('#SQM').val() ) * 4 );
				$('#QTY').blur();
			});
			$('#QTY').blur(function(){
				let kurang = eval( $('#QTY').val() ) - eval( $('#AVAILABLEQTY').val() );
				kurang = (kurang < 0) ? 0 : kurang;
				$('#LACKQTY').val( kurang );
			});
			$('#COSTSQM').blur(function(){
				$('#COST').val ( eval( $('#COSTSQM').val() ) / 4 );
				$('#TOTALCOST').val ( eval( $('#SQM').val() ) * eval( $('#COSTSQM').val() ) * eval( $('#DURATION').val() ) );
				$('#FINALCOST').val ( eval( $('#TOTALCOST').val() ) - eval( $('#DISCOUNT').val() ) );
			});
			$('#INVENTORY').change(function(){
				let targetCalendar = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getInventoryCalendar")."'; 
				let datapostCalendar = {
					INVENTORYID: $(this).val()
				}
				$.post(targetCalendar, datapostCalendar, function( e ){
					$('#AVAILABLEQTY').val( e );
					$('#AVAILABLESQM').val( e/4 );
				});

				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getInventory")."';
				let datapost = {
					INVENTORYID: $(this).val()
				}
				$.post(target, datapost, function(e){
					$('#COST').val( e.RENTALPRICE ).blur();
					$('#DISCOUNT').val( 0 ).blur();
					$('#FINALCOST').val( e.RENTALPRICE );
					$('#DESCRIPTION').val( e.PACKAGEDESCRIPTION );

				},'json');
			});
		</script>";
	}

	function getInventory(){
		$id = $this->input->post("INVENTORYID");
		$inv = $this->db->where("INVENTORYID", $id)->get("inventories")->row();
		echo json_encode($inv);
	}

	function add(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			// $_POST['INVENTORY'] = $_POST['ITEM'];
			// $_POST['DESCRIPTION'] = $_POST['SPECIFICATION'];
			$_POST['CREATEBY'] = $this->logged['USERID'];
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$project = $this->Mmasterdata->getProject($_POST['PROJECT'])['0'];
			// $this->db->insert('inventorycalendars',$sched);
			// Check Inventory
			// $this->checkInventory();
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			// $_POST['INVENTORY'] = $_POST['ITEM'];
			// $_POST['DESCRIPTION']  = $_POST['SPECIFICATION'];
			$_POST['UPDATEBY'] = $this->logged['USERID'];
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			/*if ($_POST['PROJECTSTAGE'] == "4") { # deal -> insert ke jadwal
				$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
				$project = $this->Mmasterdata->getProject($_POST['PROJECT'])['0'];
				$sched = array();
				$sched['INVENTORY'] = $_POST['INVENTORY'];
				$sched['STARTDATE'] = $project['SETUPDATE'];
				$sched['ENDDATE'] = $project['CLEARDATE'];
				$sched['PROJECT'] = $_POST['PROJECT'];
				$sched['USEDCOUNT'] = $_POST['QTY'];
				$sched['DESCRIPTION'] = $_POST['DESCRIPTION'];
				$this->db->insert('inventorycalendars',$sched);
			}*/
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
	
	function sendnotif(){
		$projectID = $this->session->userdata("PROJECTID");
		$project = $this->Mmasterdata->getProjectDetail( $projectID );
		$pdf_link = $this->generatePrint();
		$message = $project->EMPLOYEENAME ." meminta approval untuk quotation [".$project->PROJECTORDERCODE."], atas nama client ".$project->CUSTOMERNAME.". Mohon untuk diperiksa di applikasi atau lewat file pdf berikut : ".$pdf_link;
		$target = $this->Mmasterdata->getConfigItem("MARKETINGMANAGERNUMBER");
	 	// $this->Mmasterdata->sendWAblas( $target, $message);
	 	$buttons = ["Setujui", "Tolak"];
	 	$send = $this->Mmasterdata->sendButtonWablas( $target, $message, $buttons );
		echo "<script>
				swal.fire({
					icon: 'success',
					title: 'Notifikasi WA Berhasil Dikirimkan ke Manager Marketing',
					showConfirmButton: !1,
					timer: 1500
					}).then(function(){
					loadcontent('project_quotation-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
			</script>";
	}

	function approve(){
		if( $this->logged['PRIVILEGE'] == "MM" || $this->logged['PRIVILEGE'] == "ADM" ){
			$projectID = $this->session->userdata("PROJECTID");
			$project = $this->Mmasterdata->getProjectDetail( $projectID );
			$this->db->set("APPROVALSTATUS",1)->where('PROJECTID',$projectID)->update('projects');
			$message = "Qoation dengan kode [".$project->PROJECTORDERCODE."], atas nama client ".$project->CUSTOMERNAME." SUDAH DIAPPROVE dan bisa dikirmkan ke clien";
			$target = $this->db->where('EMPLOYEEID',$project->EMPLOYEE)->get('employees')->row()->MOBILE;
			$target = $phone = preg_replace('/\D+/', '', $target);
			$this->Mmasterdata->sendWAblas( $target, $message);
			echo "<script>
					swal.fire({
						icon: 'success',
						title: 'Berhasil Melakukan Persetujan Approval',
						showConfirmButton: !1,
						timer: 1500
						}).then(function(){
						loadcontent('project_quotation-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
						});
				</script>";
		}
		else{
			show_404();	
		}
	}

	function reject(){
		if( $this->logged['PRIVILEGE'] == "MM" || $this->logged['PRIVILEGE'] == "ADM" ){
			$projectID = $this->session->userdata("PROJECTID");
			$project = $this->Mmasterdata->getProjectDetail( $projectID );
			if (!empty($_POST)) {
				$this->db->set("APPROVALSTATUS",2)->set('REJECTREASON',$_POST['REASON'])->where('PROJECTID',$projectID)->update('projects');
				$message = "Qoation dengan kode ".$project->PROJECTORDERCODE.", atas nama client ".$project->CUSTOMERNAME." TIDAK DIAPPROVE dengan alasan : ".$_POST['REASON'];
				$target = $this->db->where('EMPLOYEEID',$project->EMPLOYEE)->get('employees')->row()->MOBILE;
				$target = $phone = preg_replace('/\D+/', '', $target);
				$this->Mmasterdata->sendWAblas( $target, $message);
			}
			else{
				echo "<script>
					swal.fire({
			        icon: 'info',
			        inputLabel: 'Tuliskan Alasan Penolakan',
			        title: 'Alasan Penolakan',
			        input: 'textarea',
			        showCancelButton: 'true',
			        target: '#popupModal'
			      }).then(function( data ){
			      	$.post('".site_url($this->router->fetch_directory().$this->router->fetch_class())."/reject', {REASON:data.value}, function (e) {
			      		swal.fire({
						icon: 'success',
						title: 'Berhasil Melakukan Penolakan Approval',
						showConfirmButton: !1,
						timer: 1500
						}).then(function(){
						loadcontent('project_quotation-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
						});
			      	});
			      	
			      });
				</script>";
			}
		}
		else{
			show_404();	
		}
	}

	function sendnotifclient(){
		if( $this->logged['PRIVILEGE'] == "MM" || $this->logged['PRIVILEGE'] == "ADM" ){
			$office_name = $this->Mmasterdata->getConfigItem('OFFICE_NAME');
			$projectID = $this->session->userdata("PROJECTID");
			$project = $this->Mmasterdata->getProjectDetail( $projectID );
			$this->db->set("APPROVALSTATUS",1)->where('PROJECTID',$projectID)->update('projects');
			$pdf_link = $this->generatePrint();
			$message = "Berikut lampiran penawaran dari ".$office_name." 

Nama client : ".$project->CUSTOMERNAME."
Link download penawaran : ".$pdf_link;
			// $target = "082225566148";
			$target = $project->CUSTOMERCONTACTPHONE;
			if ($target=="") {
				echo "<script>
					swal.fire({
						icon: 'danger',
						title: 'Kontak Kostumer tidak ditemukan, silahakan isikan terlebih dahulu',
						showConfirmButton: !1,
						timer: 3000
						}).then(function(){
						loadcontent('project_quotation-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
						});
				</script>";
				exit;
			}
			$target = $phone = preg_replace('/\D+/', '', $target);
			$this->Mmasterdata->sendWAblas( $target, $message);
			echo "<script>
					swal.fire({
						icon: 'success',
						title: 'Berhasil Mengirimkan Data Quotation ke klien',
						showConfirmButton: !1,
						timer: 1500
						}).then(function(){
						loadcontent('project_quotation-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
						});
				</script>";
		}
		else{
			show_404();	
		}
	}

	function print(){
		$urldownload = $this->generatePrint();
		// echo "<script>window.location.href='".$urldownload."'</script>";
		echo "<script>window.open('".$urldownload."','_blank').focus()</script>";
		echo "<script>loadcontent('project_quotation-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');</script>";

	}

	function generatePrint(){
		$projectID = $this->session->userdata("PROJECTID");
		$p = $this->Mmasterdata->getProjectDetail( $projectID );
		$pq = $this->Mmasterdata->getProjectQuotationDetailed($projectID);
		$code = $p->PROJECTORDERCODE;
		$client = $p->CUSTOMERNAME;
		$ccname = $p->CUSTOMERCONTACTNAME;
		$ccphone = $p->CUSTOMERCONTACTPHONE;
		$ccemail = $p->CUSTOMERCONTACTEMAIL;
		$currdate = date('Y-m-d');
		$date = date_to_ID($currdate);
		$grdate = $p->GRDATE;
		$grvalue = $p->GRVALUE;
		/*$validuntil = date('Y-m-d', strtotime($currdate. ' + 4 days'));
		$validuntilid = date_to_ID($validuntil);*/
		$location = $p->PROJECTLOCATION;
		$split_lokasi = $this->Mmasterdata->splitLokasi($location);
		$simple_location =  $split_lokasi.", ".$p->PROJECTROOM;
		$setupdate = $p->SETUPDATE;
		$eventdate = $p->PROJECTSTART;
		$pstart = $p->PROJECTSTART;
		$pend = $p->PROJECTEND;
		if ($pend==$pstart) {
			$tgl_event = date_to_ID($pstart);
		}
		else{
			$tgl_event = substr(date_to_ID($pstart),0,-4).'- '.date_to_ID($pend);
		}


		## durasi sewa
		// $pstart = date_create($p->PROJECTSTART);
		// $pend = date_create($p->PROJECTEND);
		// $freq = date_diff($pstart,$pend);

		$master_source = FCPATH."reportmaster/quote_master.xlsx";
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($master_source);
		$spreadsheet->getActiveSheet()->setCellValue('A6', $client);
		$spreadsheet->getActiveSheet()->setCellValue('A8', $ccname);
		$spreadsheet->getActiveSheet()->setCellValue('A10', $ccphone);
		$spreadsheet->getActiveSheet()->setCellValue('A12', $ccemail);
		$spreadsheet->getActiveSheet()->setCellValue('E6', $p->PROJECTORDERCODE);
		$spreadsheet->getActiveSheet()->setCellValue('E8', 'Surat Penawaran');
		$spreadsheet->getActiveSheet()->setCellValue('E10', date_to_id($p->QUOTATIONDATE));
		$spreadsheet->getActiveSheet()->setCellValue('E12', date_to_id($p->QUOTATIONENDDATE));
		$spreadsheet->getActiveSheet()->setCellValue('A20', 'Tanggal GR : '.date_to_ID($grdate,0,1));
		$spreadsheet->getActiveSheet()->setCellValue('A21', 'Lokasi : '.$simple_location);
		$spreadsheet->getActiveSheet()->setCellValue('A22', 'Setup Loading : '.date_to_ID($setupdate,0,1));
		$spreadsheet->getActiveSheet()->setCellValue('A23', 'Tanggal Event : '.$tgl_event);
		$spreadsheet->getActiveSheet()->setCellValue('D39', $p->EMPLOYEENAME);
		$spreadsheet->getActiveSheet()->setCellValue('D40', $p->MOBILE);
		$fcol = 'A';
		$frow = '19';
		$insert_count = 3;
		if (!empty($pq)) {
			$totalcost = array_sum(array_column($pq,'TOTAL'));
			$discount = array_sum(array_column($pq,'DISCOUNT'));
			$after_discount = $totalcost-$discount;
			$ppn = $p->TAXPERCENT/100*$after_discount;
			$grand_total = $after_discount+$ppn;
			$spreadsheet->getActiveSheet()->setCellValue('I19', $totalcost);
			$spreadsheet->getActiveSheet()->setCellValue('I20', $discount);
			$spreadsheet->getActiveSheet()->setCellValue('H21', "PPN (".$p->TAXPERCENT."%)");
			$spreadsheet->getActiveSheet()->setCellValue('I21', $ppn);
			$spreadsheet->getActiveSheet()->setCellValue('I22', $grand_total); // ini total
			$spreadsheet->getActiveSheet()->setCellValue('I23', $grvalue);
			$spreadsheet->getActiveSheet()->setCellValue('I24', $grvalue+$grand_total); // ini final atau grand total
			$spreadsheet->getActiveSheet()->insertNewRowBefore($frow,($insert_count*(count($pq))));
			foreach ($pq as $key => $value) {
				$no = $key+1;
				$loopno = $key*$insert_count;

				// $spreadsheet->getActiveSheet()->setCellValue('A'.($frow+$loopno), ""); #cell kosong atas
				$spreadsheet->getActiveSheet()->setCellValue('A'.($frow+$loopno), $no);
				$spreadsheet->getActiveSheet()->setCellValue('C'.($frow+$loopno), $value['INVENTORY']);
				if ($value['DESCRIPTION'] != "") {
					$spreadsheet->getActiveSheet()->setCellValue('C'.($frow+$loopno+1), $value['DESCRIPTION']);
					$spreadsheet->getActiveSheet()->getStyle('C'.($frow+$loopno+1))->getAlignment()->setWrapText(true);
					$rowDesc = count(explode(PHP_EOL, $value['DESCRIPTION']));
					$spreadsheet->getActiveSheet()->getRowDimension( $frow+$loopno+1 )->setRowHeight( $rowDesc * 14, "pt");
				} else {
					$spreadsheet->getActiveSheet()->setCellValue('C'.($frow+$loopno+1), "-");
				}
				if ($value['SQM']) {
					$spreadsheet->getActiveSheet()->setCellValue('D'.($frow+$loopno), $value['SQM']);
					$spreadsheet->getActiveSheet()->setCellValue('E'.($frow+$loopno), 'Sqm');
				}
				else{
					$spreadsheet->getActiveSheet()->setCellValue('E'.($frow+$loopno), $value['UNITTYPE']);
					$spreadsheet->getActiveSheet()->setCellValue('D'.($frow+$loopno), $value['QTY']);
				}
				$spreadsheet->getActiveSheet()->setCellValue('F'.($frow+$loopno), $value['DURATION']);
				$spreadsheet->getActiveSheet()->setCellValue('G'.($frow+$loopno), 'hari');
				$spreadsheet->getActiveSheet()->setCellValue('H'.($frow+$loopno), $value['COSTSQM']);
				$spreadsheet->getActiveSheet()->setCellValue('I'.($frow+$loopno), $value['TOTAL']);
				// $spreadsheet->getActiveSheet()->setCellValue('A'.($frow+$loopno+2), ""); #cell kosong bawah
			}
		}

		// $spreadsheet->getActiveSheet()->setCellValue('A0', $var);
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
		$filename = "QUOTE_".$code.""."_".date('Y-m-d_His').".xlsx";
		$writer->save(FCPATH."downloads/".$filename);
		$urldownload = site_url('downloads/'.$filename);
		$urlexcel = FCPATH."downloads/".$filename;
		$urlpdf = FCPATH.'downloads/'.$filename.".pdf";
		$this->Mmasterpublic->w2pdf($urlexcel, $urlpdf);
		// rename(FCPATH.'downloads/'.$pdf, FCPATH.'downloads/'.md5($pdf).'.pdf');
		$urldownload = site_url('downloads/'.$filename.".pdf/".substr($filename, 0, -4)."pdf");
		return $urldownload;

	}

}
