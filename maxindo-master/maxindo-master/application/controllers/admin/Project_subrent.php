<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_subrent extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,detail,instantprint,sendEmail";
		if( $this->logged['PRIVILEGE'] == "MRK" ){
			$project = $this->session->userdata("project");
			if( $this->logged['EMPLOYEE'] != $project->EMPLOYEE ){
				$this->params['command'] = "browse";
			}
		}
		$this->params['cmd'] = array(
				'sendEmail' => array(
					'url' => "javascript:loadcontent(\"project_subrent-content\",\"".site_url( $this->router->fetch_directory() .$this->router->fetch_class(). "/sendEmail/[urlparams]" ) . "\"".");",
					'icon' => "fa fa-envelope text-success",
				),
		);

		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_subrent";
		$this->params['simpleform'] = true;
		$this->params['table'] = "projectsubrents";
		$this->params['sql'] = "SELECT PROJECTSUBRENTID, PROJECT, vendors.NAME as VENDOR, vendors.ADDRESS ADDRESS, SUBRENTNUMBER, SUBRENTDATE,SUBRENTENDDATE, SENDSTATUS, projectsubrents.EMAIL,NOTES,
		projectsubrents.CONTACTNAME, projectsubrents.CONTACTMOBILE,SUBRENTSTATUS, PAYMENTSTATUS,
		(SELECT SUM(QTY*PRICE) FROM projectsubrentdetails WHERE projectsubrentdetails.PROJECTSUBRENT=projectsubrents.PROJECTSUBRENTID) as TOTAL_SUBRENT 
		FROM projectsubrents
		LEFT JOIN vendors ON VENDOR=VENDORID
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."'
		";
		$this->params['sqlupdate'] = "SELECT vendors.NAME as VENDORNAME, projectsubrents.*
		FROM projectsubrents
		LEFT JOIN projects ON PROJECT = PROJECTID
		LEFT JOIN customers ON CUSTOMER = CUSTOMERID
		LEFT JOIN vendors ON VENDOR=VENDORID";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();

		$this->params['sqlmaster'] = $this->params['sql'];
		// Slave Params
		$this->params['table-slave'] ="projectsubrentdetails";
		$this->params['slavecommand'] ="browse,add,edit,delete,deleteall";
		$this->params['slavename'] = $this->lang->line("projectsubrentdetail");
		$this->params['sqlslave']="SELECT PROJECTSUBRENTDETAILID,
		PROJECTSUBRENT, PRICE, projectsubrentdetails.PRICE * projectsubrentdetails.QTY * projectsubrentdetails.FREQ as TOTALPRICE, FREQ,
		inventories.NAME as INVENTORY, 
		PROJECTQUOTATION, projectsubrentdetails.QTY,
		projectquotations.LACKQTY, projectsubrentdetails.NOTES
		FROM projectsubrentdetails
		LEFT JOIN projectquotations ON PROJECTQUOTATIONID=PROJECTQUOTATION
		LEFT JOIN inventories ON projectquotations.INVENTORY=inventories.INVENTORYID
		";

		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "loadcontent(\"".$this->params['maincontent']."\", \"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/browse_detail/[urlparams]")."\")";
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTSUBRENTID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'SUBRENTNUMBER' => array(
				'class' => 'sorting'
			),
			'SUBRENTDATE' => array(
				'class' => 'sorting',
				'type' => "date"
			),
			'VENDOR' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'CONTACTNAME' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'CONTACTMOBILE' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'EMAIL' => array(
				'class' => 'sorting',
				'type' => "",
			),
			'SENDSTATUS' => array(
				'class' => 'sorting',
				'type' => "function",
				'model' => "Mmasterdata",
				'func' => "getSendEmailStatus",
				'params' => "SENDSTATUS",
				// 'width' => "150px"
			),
			'PAYMENTSTATUS' => array(
				'class' => "sorting",
			),
			'SUBRENTSTATUS' => array(
				'class' => "sorting",
			),
			'TOTAL_SUBRENT' => array(
				'class' => 'sorting',
				'type' => "number",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTSUBRENTID' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'SUBRENTNUMBER' => array(
				'class' => 'col-md-6',
				'validation' => "required",
				'value' => $this->Mmasterdata->getSubrentNumber()
			),
			'SUBRENTDATE' => array(
				'class' => 'col-md-3',
				'type' => "date",
				'value' => date('Y-m-d')
			),
			/*'SUBRENTSTARTDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'validation' => ""
			),*/
			'SUBRENTENDDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'validation' => ""
			),
			'VENDOR' => array(
				'class' => 'col-md-3 select2',
				'type' => "popup",
				'popup_url' => site_url( $this->router->fetch_directory() . "vendor_popup/index/" ),
			),
			'VENDORNAME' => array(
				'class' => "col-md-6",
				'disabled' => true
				// 'disabled' => true
			),
			'CONTACTNAME' => array(
				'class' => 'col-md-6',
				'validation' => "required"
			),
			'CONTACTMOBILE' => array(
				'class' => 'col-md-6',
				'validation' => "required"
			),
			'EMAIL' => array(
				'class' => 'col-md-6',
				'validation' => "required",
				'type' => "email",
			),
			'NOTES' => array(
				'class' => 'col-md-6',
				'type' => "textarea",
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
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			function setVendor(id, value){
				$('#' + id). val( value );
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getVendor/")."';
				let datapost = {
					id: value
				}
				$.post(target, datapost, function(e){
					console.log(e);
					$('#VENDORNAME').val( e.NAME );
					$('#VENDOR').val( e.VENDORID );
					$('#CONTACTNAME').val( e.CONTACTNAME1);
					$('#CONTACTMOBILE').val( e.CONTACTPHONE1);
					$('#EMAIL').val( e.CONTACTEMAIL1);
				},'json');
				$('#inputModal [data-bs-dismiss=\"modal\"]').click();
			}
		</script>";
	}

	function getVendor(){
		$id = $this->input->post("id");
		if( isset($id) ){
			$dataVendor = $this->db->where("VENDORID", $id)->get("vendors")->row();
			echo json_encode($dataVendor);
		}
		return false;
	}

	function add(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
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

	function fieldselectmaster(){
		$this->params['fieldselectedmaster']=array(
			'SUBRENTNUMBER' => array(

			),
			'SUBRENTDATE' => array(

			),		
			'VENDOR' => array(

			),			
		);
	}

	function fieldselectslave(){
		$this->params['fieldselectslave']=array(
			'SEQ' => array(

			),
			'#' => array(
				'type' => "checkbox"
			),
			'PROJECTSUBRENTDETAILID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'PROJECTSUBRENT' => array(
				'type' => "foreignkey",
				'hidden' => true
			),
			'INVENTORY' => array(
				'class' => "sorting",
			),
			'LACKQTY' => array(
				'class' => "sorting",
			),
			'QTY' => array(
				'class' => "sorting",
				'type' => "number",
				'align' => "left"
			),
			'FREQ' => array(
				'class' => "sorting",
				'rtag' => " hari",
				'align' => "left"
			),
			'PRICE' => array(
				'class' => "sorting",
				'type' => "number",
				'align' => "left"
			),
			'TOTALPRICE' => array(
				'class' => "sorting",
				'type' => "number",
				'align' => "left"
			),
			'NOTES' => array(
				'class' => "sorting",
			),
		);
	}
	function fieldeditslave(){
		$this->params['fieldeditslave']=array(
			'PROJECTSUBRENTDETAILID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'PROJECTSUBRENT' => array(
				'type' => "foreignkey",
				'hidden' => true,
				'value' => $this->urisegments['valpk']
			),
			'PROJECTQUOTATION' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectQuotationSubrent( $this->session->userdata("PROJECTID"))),
				'validation' => "required"
			),
			'QTY' => array(
				'class' => "col-md-3",
				'type' => "number",
				'validation' => "required"
			),
			'STARTDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'validation' => ""
			),
			'ENDDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'validation' => ""
			),
			'PRICE' => array(
				'class' => "col-md-3",
				'type' => "number",
				'validation' => "required"
			),
			'FREQ' => array(
				'class' => "col-md-2",
				'type' => "decimal",
				'value' => $this->Mmasterdata->getProjectDetail( $this->session->userdata("PROJECTID"))->PROJECTDURATION,
				// 'validation' => "required"
			),
			'NOTES' => array(
				'class' => "col-md-6",
				'type' => "textarea",
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
			),
			
		);
	}

	function browse_detail(){
		$this->params['primarykeymaster']=$this->urisegments['pk'];
		$this->params['valprimarykeymaster']=$this->urisegments['valpk'];
		$this->fieldselectmaster();
		$this->fieldselectslave();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse_detail();
	}
	function add_detail(){
		$this->params['primarykeymaster']=$this->urisegments['pk'];
		$this->params['valprimarykeymaster']=$this->urisegments['valpk'];
		$this->fieldselectmaster();
		$this->fieldeditslave();
		if (count($_POST)>0){
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add_detail();
	}

	function edit_detail(){
		$this->params['primarykeymaster']= $this->urisegments['fk'] . "ID";
		$this->params['valprimarykeymaster']=$this->urisegments['valfk'];
		$this->fieldselectmaster();
		$this->fieldeditslave();
		$this->params['command']="browse";
		if (count($_POST)>0){
			$_POST['UPDATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->edit_detail();
	}

	function delete_detail(){
		$delete=$this->db->delete($this->params['table-slave'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse_detail/pk/".$this->urisegments['fk']."ID/valpk/".urldecode($this->urisegments['valfk'])."/');
			</script>";
		}
	}

	function deleteall_slave(){
		$post = $this->input->post();
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$this->db->delete($this->params['table-slave'], array($pk => $val));
			}
		}
		echo "<script>
			loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse_detail/pk/".$this->urisegments['pk']."/valpk/".urldecode($this->urisegments['valpk'])."/');
		</script>";
	}

	function generatePrint($args){
		$projectID = $this->session->userdata("PROJECTID");
		$p = $this->Mmasterdata->getProjectDetail( $projectID );
		$ps = $this->db->query($this->params['sql'])->row();
		$vpkm=$args['valpk'];
		$psd = $this->db->query($this->params['sqlslave']." WHERE PROJECTSUBRENT = '".$vpkm."'")->result_array();
		$code = $ps->SUBRENTNUMBER;
		$currdate = date('Y-m-d');;
		$location = $p->PROJECTLOCATION;
		$split_lokasi = $this->Mmasterdata->splitLokasi($location);
		$simple_location =  $split_lokasi.", ".$p->PROJECTROOM;
		$setupdate = $p->SETUPDATE;
		$displacedate = $p->DISPLACEDATE;
		$eventdate = $p->PROJECTSTART;
		$pstart = $ps->SUBRENTDATE; //$p->PROJECTSTART;
		$pend =  $ps->SUBRENTENDDATE; //$p->PROJECTSTART;
		$master_source = FCPATH."reportmaster/po_master.xlsx";
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($master_source);
		$spreadsheet->getActiveSheet()->setCellValue('E4', ': '.date_to_ID($currdate));
		$spreadsheet->getActiveSheet()->setCellValue('E5', ': '.$ps->SUBRENTNUMBER);
		$spreadsheet->getActiveSheet()->setCellValue('A10', $ps->VENDOR);
		$spreadsheet->getActiveSheet()->setCellValue('A11', $ps->ADDRESS);
		$spreadsheet->getActiveSheet()->setCellValue('A12', $ps->CONTACTNAME);
		$spreadsheet->getActiveSheet()->setCellValue('A13', $ps->CONTACTMOBILE);
		$spreadsheet->getActiveSheet()->setCellValue('B19', " : ".date_to_ID($pstart));
		$spreadsheet->getActiveSheet()->setCellValue('B20', " : ".date_to_ID($pend));
		$spreadsheet->getActiveSheet()->setCellValue('B21', " : ".date_to_ID($setupdate,0,1));
		$spreadsheet->getActiveSheet()->setCellValue('B22', " : ".$simple_location);
		$spreadsheet->getActiveSheet()->setCellValue('B23', " : ".$p->EMPLOYEENAME." (".$p->MOBILE.")"); #sales
		$spreadsheet->getActiveSheet()->setCellValue('B24', ' : '.$ps->NOTES);



		$fcol = 'A';
		$frow = '18';
		$insert_count = 2;
		if (!empty($psd)) {
			$totalcost = array_sum(array_column($psd,'TOTALPRICE'));
			$discount = '0';
			$total = $totalcost-$discount;
			$spreadsheet->getActiveSheet()->setCellValue('F18', $totalcost);
			$spreadsheet->getActiveSheet()->setCellValue('F19', $discount);
			$spreadsheet->getActiveSheet()->setCellValue('F20', $total);
			$spreadsheet->getActiveSheet()->insertNewRowBefore($frow,($insert_count*(count($psd))));
			foreach ($psd as $key => $value) {
				$no = $key+1;
				$loopno = $key*$insert_count;
				$spreadsheet->getActiveSheet()->setCellValue('A'.($frow+$loopno), $no);
				$spreadsheet->getActiveSheet()->setCellValue('B'.($frow+$loopno), $value['INVENTORY']);
				if ($value['NOTES'] != "") {
					$spreadsheet->getActiveSheet()->setCellValue('B'.($frow+$loopno+1), $value['NOTES']);
					$spreadsheet->getActiveSheet()->getStyle('B'.($frow+$loopno+1))->getAlignment()->setWrapText(true);
					$rowDesc = count(explode(PHP_EOL, $value['NOTES']));
					$spreadsheet->getActiveSheet()->getRowDimension( $frow+$loopno+1 )->setRowHeight( $rowDesc * 14, "pt");
				} else {
					$spreadsheet->getActiveSheet()->setCellValue('B'.($frow+$loopno+1), "-");
				}
				$spreadsheet->getActiveSheet()->setCellValue('C'.($frow+$loopno), $value['QTY']);
				$spreadsheet->getActiveSheet()->setCellValue('D'.($frow+$loopno), $value['FREQ']." hari");
				$spreadsheet->getActiveSheet()->setCellValue('E'.($frow+$loopno), $value['PRICE']);
				$spreadsheet->getActiveSheet()->setCellValue('F'.($frow+$loopno), $value['TOTALPRICE']);
				$spreadsheet->getActiveSheet()->getStyle('A'.($frow+$loopno+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('B'.($frow+$loopno+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('C'.($frow+$loopno+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('D'.($frow+$loopno+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('E'.($frow+$loopno+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('F'.($frow+$loopno+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			}
			$spreadsheet->getActiveSheet()->removeRow(16, 2);
		}

		// $spreadsheet->getActiveSheet()->setCellValue('A0', $var);
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
		$filename = "PO_".$code.""."_".date('Y-m-d_His').".xlsx";
		$writer->save(FCPATH."downloads/".$filename);
		$urldownload = site_url('downloads/'.$filename);
		$urlexcel = FCPATH."downloads/".$filename;
		$urlpdf = FCPATH.'downloads/'.$filename.".pdf";
		$this->Mmasterpublic->w2pdf($urlexcel, $urlpdf);
		// rename(FCPATH.'downloads/'.$pdf, FCPATH.'downloads/'.md5($pdf).'.pdf');
		$urldownload = site_url('downloads/'.$filename.".pdf/".substr($filename, 0, -4)."pdf");
		return array($urldownload,$urlpdf);


	}

	function print(){
		$urldownload = $this->generatePrint($this->urisegments)['0'];
		// echo "<script>window.location.href='".$urldownload."'</script>";
		echo "<script>window.open('".$urldownload."','_blank').focus()</script>";
		echo "<script>loadcontent('project_subrent-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');</script>";

	}

    function sendEmail() {
    	$vpkm=$this->urisegments['valpk'];
		$to_email = $this->db->where('PROJECTSUBRENTID',$vpkm)->get('projectsubrents')->row()->EMAIL;
		if (!$to_email) {
			echo "<script>
				swal.fire({
					icon: 'warning',
					title: 'Mohon isi alamat email vendor.',
					showConfirmButton: !1,
					timer: 3000
					}).then(function(){
					loadcontent('project_subrent-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
			</script>";
			exit;
		}
    	// $from_email = $this->Mmasterdata->getConfigItem('OFFICE_EMAIL');
        $from_email = "acerola.rx@gmail.com";
        $file = $this->generatePrint($this->urisegments)['0'];
        //Load email library
        $this->load->library('email');
        $this->email->from($from_email, 'Maxindo LED');
        $this->email->to($to_email);
        $this->email->subject('PURCHASE ORDER');
        $this->email->message('berikut kami lampirkan file pdf');
        $this->email->attach($file);
		$send = $this->email->send();
		if ($send) {
			$this->db->where('PROJECTSUBRENTID',$vpkm)->set('SENDSTATUS','1')->update('projectsubrents');
			echo "<script>
				swal.fire({
					icon: 'success',
					title: 'Email terkirim.',
					showConfirmButton: !1,
					timer: 1500
					}).then(function(){
					loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
			</script>";
		}
		else{
			echo "<script>
				swal.fire({
					icon: 'warning',
					title: 'Email tidak terkirim!',
					showConfirmButton: 1,
					message: '".dd($send)."'
					}).then(function(){
					loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
			</script>";
		}

    }

}
