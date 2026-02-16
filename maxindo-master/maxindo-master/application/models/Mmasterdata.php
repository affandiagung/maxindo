<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Curl\Curl;

class Mmasterdata extends CI_Model{
	private $logged = array();

	function __construct(){
		parent::__construct();
		$this->logged = $this->session->userdata("admin");
	}

	function getOrderSeq( $max = 100 ){
		$result =  array();
		for($i = 1; $i <= $max; $i++){
			$result[$i] = $i;
		}
		return $result;
	}

	function getMenu( $privilege, $top = false ){
		if( $top == true ){
			$this->db->where("PARENTMENU", 0);
		}
		return $this->db->select("menus.NAME, menus.URL, menus.ICON, menus.MENUID, menuprivileges.MENUPRIVILEGEID, menuprivileges.PARENTMENU, menuprivileges.MENU, NEWTAB")
		->where("PRIVILEGE", $privilege)
		->join("menus", "MENU=MENUID", "LEFT")
		->join("privileges", "PRIVILEGE=PRIVILEGEID", "LEFT")
		->order_by("ORDERSEQ")
		->get("menuprivileges")->result();
	}

	function getChildMenu( $menuid, $privilegeid ){
		return $this->db->select("
			menus.NAME, menus.URL, menus.ICON, menus.MENUID, menuprivileges.MENUPRIVILEGEID, menuprivileges.PARENTMENU, menuprivileges.MENU
			")
		->where("PARENTMENU", $menuid)
		->where("PRIVILEGE", $privilegeid)
		->join("menus", "MENU=MENUID", "LEFT")
		->join("privileges", "PRIVILEGE=PRIVILEGEID", "LEFT")
		->order_by("ORDERSEQ")
		->get("menuprivileges")->result();
	}

	function getHomePage( $privilege ){
		$homepage = site_url();
		$privileges = $this->db->where("PRIVILEGEID", $privilege)->get("privileges")->result();
		if( count($privileges) > 0 ){
			$homepage .= $privileges[0]->HOMEDIR;
		}
		return $homepage;
	}

	function getAccessPrivilege( $privilege ){
		$directory = "";
		$privileges = $this->db->where("PRIVILEGEID", $privilege)->get("privileges")->result();
		if( count($privileges) > 0 ){
			$directory = $privileges[0]->HOMEDIR;
		}
		return $directory;
	}

	function getPrivilege(){
		$privileges = $this->db->get("privileges")->result();
		$result = array();
		foreach($privileges as $priv){
			$result[$priv->PRIVILEGEID] = $priv->NAME;
		}
		return $result;
	}

	function getLoggedUser( $userid ){
		return $this->db->select("users.*, privileges.NAME as PRIVILEGENAME")
		->join("privileges", "PRIVILEGE=PRIVILEGEID", "LEFT")
		->where("USERID", $userid)
		->get("users")->row();
	}

	function getMonth(){
		$month = array(
			'01' => "Januari",
			'02' => "Februari",
			'03' => "Maret",
			'04' => "April",
			'05' => "Mei",
			'06' => "Juni",
			'07' => "Juli",
			'08' => "Agustus",
			'09' => "September",
			'10' => "Oktober",
			'11' => "November",
			'12' => "Desember",
		);
		return $month;
	}

	function getMonthRome(){
		$month = array(
			'01' => "I",
			'02' => "II",
			'03' => "III",
			'04' => "IV",
			'05' => "V",
			'06' => "VI",
			'07' => "VII",
			'08' => "VIII",
			'09' => "IX",
			'10' => "X",
			'11' => "XI",
			'12' => "XII",
		);
		return $month;
	}

	function getDay(){
		$days = array(
			'0' => "Minggu",
			'1' => "Senin",
			'2' => "Selasa",
			'3' => "Rabu",
			'4' => "Kamis",
			'5' => "Juma't",
			'6' => "Sabtu",
		);
		return $days;
	}

	function getAgama(){
		return array(
			'1' => "Islam",
			'2' => "Kristen",
			'3' => "Katholik",
			'4' => "Hindu",
			'5' => "Budha",
			'6' => "Lainnya"
			);
	}

	function getGender(){
		return array(
			'L' => "Laki-laki",
			'P' => "Perempuan"
		);
	}

	function getKewarganegaraan(){
		return array(
			'WNI' => "WNI - Warga Negara Indonesia",
			'WNA' => "WNA - Warga Negara Asing"
		);
	}

	function getStatusHidup(){
		return array(
			'1' => "Hidup",
			'0' => "Meninggal"
		);
	}

	function getStatusSimple(){
		return array(
			'1' => "Ya",
			'0' => "Tidak",
		);
	}

	function pushToFirebase($title,$body,$url){
		$headers = array(
				 'Authorization:key=' .$this->config->item('firebase_server_key'),
				 'Content-Type:application/json');
		$fields = array(
			'to' => '/topics/punglor',
				'notification' => array(
					'title'=>$title,
					'body'=>$body,
				),
				'data' => array(
					'url' => $url
				),
			 );
		$payload=json_encode($fields);
		$curl_session = curl_init();
		curl_setopt($curl_session, CURLOPT_URL, $this->config->item('firebase_push_url'));
		curl_setopt($curl_session, CURLOPT_POST, true);
		curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
		$result = curl_exec($curl_session);
		return $result;
	}

	function callAPI($url, $method = "GET", $data = array(), $authKey = null){
    $request_headers = array();
    if( isset($authKey) ){
    	$request_headers[] = 'Authorization: Bearer ' . $secretKey;
    }
    $ch = curl_init();
    if( $method == "POST" ){
    	curl_setopt($ch, CURLOPT_POST, true);
    }
    if( count($data) > 0 ){
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
		print_r($data);
    if (curl_errno($ch)){
      print "Error: " . curl_error($ch);
    } else {
      $result = json_decode($data, TRUE);
      curl_close($ch);
      return $result;
    }
	}

	function getEducationLevel(){
		return array(
			'SD' => "Sekolah Dasar",
			'SMP' => "Sekolah Menengah Pertama",
			'SMA' => "Sekolah Menengah Atas", 
			'D1' => "Diploma 1",
			'D2' => "Diploma 2",
			'D3' => "Diploma 3",
			'S1' => "Sarjana",
			'S2' => "Magister",
			'S3' => "Profesor / Doktor",
			'L' => "Pendidikan Lainnya",
		);
	}

	function getActiveStatus(){
		return array(
			'0' => "<span class='badge badge-light-danger'>Tidak</span>",
			'1' => "<span class='badge badge-light-success'>Aktif</span>"
		);
	}

	function getPaymentStatus(){
		return array(
			'0' => "<span class='badge badge-light-danger'>Belum</span>",
			'1' => "<span class='badge badge-light-success'>Lunas</span>",
		);
	}

	function getSubrentStatus(){
		return array(
			'0' => "<span class='badge badge-light-danger'>Draft</span>",
			'1' => "<span class='badge badge-light-info'>Process</span>",
			'2' => "<span class='badge badge-light-success'>Done</span>",
		);
	}

	function getUserStatus(){
		return array(
			'1' => "<span class='badge badge-light-success'>Aktif</span>",
			'0' => "<span class='badge badge-light-danger'>Tidak Aktif</span>",
		);
	}

	function getStatus(){
		return array(
			'0' => "<span class='badge badge-light-danger'>Belum</span>",
			'1' => "<span class='badge badge-light-success'>Ya</span>",
		);
	}

	function getYesNo(){
		return array(
			'0' => "<span class='badge badge-light-danger'>Tidak</span>",
			'1' => "<span class='badge badge-light-success'>Ya</span>",
		);
	}

	function getApprovalStatus(){
		return array(
			'0' => "<span class='badge badge-light-warning'>Belum Diperiksa</span>",
			'1' => "<span class='badge badge-light-success'>Diterima</span>",
			'2' => "<span class='badge badge-light-danger'>Ditolak</span>",
		);
	}


	function getParentMenu( $privilege ){
		return $this->db->select("MENUPRIVILEGEID as keydt, menus.NAME as valuedt")->where("MENULEVEL", '0')->get("menus")->result_array();
	}

	function getAllMenu(){
		return $this->db->select("MENUID as keydt, CONCAT(menus.NAME, ' -> ', URL) as valuedt")->get("menus")->result_array();
	}

	function sendWA($phoneNumber, $message){
		$apiKey = "67da02e6cf92251cb90e0161e3cd55842828482b514434c394824e1e38fa6caa";
		$url = "https://sendtalk-api.taptalk.io/api/v1/message/send_whatsapp";

		$data = [
			'phone' => $phoneNumber,
			'messageType' => "text",
			'body' => $message
		];

		$curl = new Curl();
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setHeader('API-Key', $apiKey );
		$curl->post('https://sendtalk-api.taptalk.io/api/v1/message/send_whatsapp', $data);
		$result = "Terima Kasih, pesan Anda sudah kami terima";
		// dd($curl->response);
		if ($curl->error) {
			$result = "<div class='alert alert-danger'>Error: " . $curl->errorCode . ": " . $curl->errorMessage . "</div>";
		} else {
			if( $curl->response->status == "200" ){
				$result = "<div class='alert alert-success'>Terima kasih, permintaan Anda segera diproses. Kami akan menghubungi Anda beberapa saat lagi.</div>";
			} else {
				$result = "<div class='alert alert-danger'>" . $curl->response->error->message . "</div>";
			}
		}

	}


	function sendWAblas($phoneNumber, $message){
		$data = [
	    "data" => [
        [
            'phone' => $phoneNumber,
            'message' => $message,
        ],
	    ]
		];
		/*$data = [
			"phone" => $phoneNumber,
			"message" => $message,
			"token" => $this->config->item("wb_api_key")
		];*/
		$curl = new Curl();
		$curl->setHeaders( array(
			'Content-Type' => 'application/json',
			'Authorization' => $this->config->item("wb_api_key")
		));
		$curl->post('https://kudus.wablas.com/api/v2/send-message', json_encode($data));
		$result = "Terima Kasih, pesan Anda sudah kami terima";
		if ($curl->error) {
			$result = "Error: " . $curl->errorCode . ": " . $curl->errorMessage;
		} else {
			if( $curl->response->status == "200" ){
				// $result = "success";
				$result = $curl->response;
			} else {
				$result = $curl->response->error->message;
			}
		}
		return $result;
	}

	function sendButtonWablas( $phoneNumber, $message, $buttons = array() ){
		// $payload = [
		//     "data" => [
	  //       [
    //         'phone' => $phoneNumber,
    //         'message' => [
    //           'buttons' => $buttons,
    //           'content' => $message,
    //           'footer' => $this->getConfigItem("APP_NAME"),
    //         ],
	  //       ]
		//     ]
		// ];
		$message .= "\n\nSilakan Balas / Reply pesan ini dengan \n'Setujui' atau 'Tolak'";
		$payload = [
	    "data" => [
        [
            'phone' => $phoneNumber,
            'message' => $message,
        ],
	    ]
		];
		$curl = new Curl();
		$curl->setHeaders( array(
			'Content-Type' => 'application/json',
			'Authorization' => $this->config->item("wb_api_key")
		));
		// $curl->post('https://kudus.wablas.com/api/v2/send-button', json_encode($payload));
		$curl->post('https://kudus.wablas.com/api/v2/send-message', json_encode($payload));
		$result = "Terima Kasih, pesan Anda sudah kami terima";
		if ($curl->error) {
			$result = "Error: " . $curl->errorCode . ": " . $curl->errorMessage;
		} else {
			if( $curl->response->status == "200" ){
				// $result = "success";
				$result = $curl->response;
			} else {
				$result = $curl->response->error->message;
			}
		}
		return $result;
	}


	function sendEmail( $email, $subject, $msg){
		$subject = strip_tags($subject);
		$configs = $this->db->get("configurations")->result();
		$config = $configs[0];

		$mail = new PHPMailer(true);      
		try {
	    //Server settings
	    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = $config->SMTP_HOST;  										// Specify main and backup SMTP servers
	    $mail->SMTPAuth = true;                               // Enable SMTP authentication
	    $mail->Username = $config->SMTP_USER;        					// SMTP username
	    $mail->Password = $config->SMTP_PASSWORD;             // SMTP password
	    $mail->SMTPSecure = $config->SMTP_SECURE;             // Enable TLS encryption, `ssl` also accepted
	    $mail->Port = $config->SMTP_PORT;                     // TCP port to connect to

	    //Recipients
	    $mail->setFrom($config->SMTP_USER, $config->SMTP_NAME);
	    $mail->addAddress($email);     // Add a recipient

	    // $mail->addReplyTo('info@example.com', 'Information');
	    // $mail->addCC('cc@example.com');
	    // $mail->addBCC('bcc@example.com');

	    //Attachments
	    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = $subject;
	    $data['msg'] = $msg;
	    $mail->Body  = $this->load->view("mail", $data, true);
	    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	    $mail->send();
	    return true;
		} catch (Exception $e) {
	    // echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	    return false;
		}

	}

	function getConfiguration(){
		return $this->db->get("configurations")->row();
	}

	function getConfigItem( $field ){
		$config = $this->db->select($field)->get("configurations")->row();
		return $config->$field;
	}
	
	function getAdmStatus( $filter = null ){
		if(isset($filter)){
			foreach($filter as $f){
				$this->db->or_where("ADMSTATUSID", $f);
			}
		}
		return $this->db->select("ADMSTATUSID as keydt, NAME as valuedt")->get("admstatuses")->result_array();
	}

	function getProvince(){
		return $this->db->select("PROVINCEID as keydt, NAME as valuedt")->get("provinces")->result_array();
	}

	function getCity( $cityid = null){
		if( isset($cityid) ){
			$this->db->where("CITYID", $cityid);
		}
		return $this->db->select("CITYID as keydt, cities.NAME as valuedt, provinces.NAME as labeldt")->join("provinces", "PROVINCEID=PROVINCE", "LEFT")->get("cities")->result_array();
	}

	function getDistrict(){
		return $this->db->select("DISTRICTID as keydt, districts.NAME as valuedt, cities.NAME as labeldt")->join("cities", "CITYID=CITY", "LEFT")->get("districts")->result_array();
	}

	function getVillage(){
		return $this->db->select("VILLAGEID as keydt, villages.NAME as valuedt, districts.NAME as labeldt")->join("districts", "DISTRICTID=DISTRICT", "LEFT")->get("villages")->result_array();
	}

	function getBlank( $msg = "" ){
		return array(0 => array("keydt" => "", "valuedt" => "- Pilih " . $msg . " -"));
	}

	function getTahun(){
		return $this->db->select("ID as keydt, NAMA as valuedt")->order_by("ID","DESC")->get("tahun")->result_array();
	}

	function getTahunAktif(){
		return $this->db->select("ID, NAMA")->where("AKTIF", 1)->get("tahun")->row();
	}


	/* APP CUSTOM */

	function getStockOpnameStatus(){
		return array(
			'0' => "<span class='badge badge-light-danger'>Belum Diproses</span>",
			'1' => "<span class='badge badge-light-success'>Sudah Diproses</span>",
		);
	}


	function getUnit(){
		return $this->db->select("UNITID as keydt, NAME as valuedt")->get("units")->result_array();
	}

	function getProjectUnit(){
		return $this->db->select("PROJECTUNITID as keydt, NAME as valuedt")->get("projectunits")->result_array();
	}

	function getJobPosition(){
		return $this->db->select("JOBPOSITIONID as keydt, NAME as valuedt")->get("jobpositions")->result_array();
	}

	function getVendor(){
		return $this->db->select("VENDORID as keydt, NAME as valuedt")->get("vendors")->result_array();
	}

	function getCustomer(){
		return $this->db->select("CUSTOMERID as keydt, NAME as valuedt")->get("customers")->result_array();
	}

	function getCustomerData(){
		return $this->db->select("CUSTOMERID as keydt, NAME as valuedt")->get("customers")->result_array();
	}


	function getEmployee(){
		return $this->db->select("EMPLOYEEID as keydt, CONCAT(EMPLOYEECODE,' - ',employees.NAME) as valuedt, units.NAME as labeldt")
		->join("units", "UNIT=UNITID", "LEFT")
		->order_by("units.NAME")
		->get("employees")->result_array();
	}

	function getRelation(){
		return $this->db->select("RELATIONID as keydt, NAME as valuedt")->get("relations")->result_array();
	}

	function getProjectNameById($id=NULL){
		return $this->db->where("PROJECTID",$id)->get("projects")->row()->NAME;
	}

	function getInventoryNameById($id=NULL){
		return $this->db->where("INVENTORYID",$id)->get("inventories")->row()->NAME;
	}

	function getEmployeeNameById($id=NULL){
		return $this->db->where("EMPLOYEEID",$id)->get("employees")->row()->NAME;
	}

	function getInventory( $all = false ){
		if( $all == false ){
			$this->db->where("TRXSTATUS", 1);
		}
		return $this->db->select("INVENTORYID as keydt, NAME as valuedt")->get("inventories")->result_array();
	}

	function getInventoryDetail(){
		return $this->db->select("INVENTORYDETAILID as keydt, CONCAT(inventorydetails.BARCODE,' - QTY : ', QTY) as valuedt, inventories.NAME as labeldt")
		->join("inventories", "INVENTORY=INVENTORYDETAILID", "LEFT")
		->order_by("inventories.INVENTORYID", "ASC")
		->get("inventorydetails")->result_array();
	}

	function getInventoryType(){
		return $this->db->select("INVENTORYTYPEID as keydt, NAME as valuedt")->get("inventorytypes")->result_array();
	}

	function getInventoryStatus(){
		return $this->db->select("INVENTORYSTATUSID as keydt, NAME as valuedt")->get("inventorystatuses")->result_array();
	}

	function getSupplier(){
		return $this->db->select("SUPPLIERID as keydt, NAME as valuedt")->get("suppliers")->result_array();
	}

	function getInsurance(){
		return $this->db->select("INSURANCEID as keydt, NAME as valuedt")->get("insurances")->result_array();
	}

	function getInventoryCondition(){
		return $this->db->select("INVENTORYCONDITIONID as keydt, NAME as valuedt")->get("inventoryconditions")->result_array();
	}

	function getProjectstage( $max = null ){
		if( isset($max) ){
			$this->db->where("PROJECTSTAGEID <=", $max);
		}
		return $this->db->select("PROJECTSTAGEID as keydt, NAME as valuedt")->get("projectstages")->result_array();
	}

	function getProjectType(){
		return $this->db->select("PROJECTTYPEID as keydt, NAME as valuedt")->get("projecttypes")->result_array();
	}

	function getActivityType(){
		return $this->db->select("ACTIVITYTYPEID as keydt, NAME as valuedt")->get("activitytypes")->result_array();
	}

	function getProjectOrderCode(){
		$code = substr(md5(microtime()),5,6);
		$check = $this->db->where("PROJECTORDERCODE", $code)->get("projects")->num_rows();
		if( $check > 0 ){
			$this->getProjectOrderCode();
		} else {
			return $code;
		}
	}

	function getTotalIncome( $tglAwal, $tglAkhir ){
		$payment = $this->db->select("SUM(IFNULL(PAYMENTAMOUNT,0)) as TOTPAYMENTAMOUNT")->where("PAYMENTDATE >=", $tglAwal)->where("PAYMENTDATE <=", $tglAkhir)->get("payments")->row();
		return $payment->TOTPAYMENTAMOUNT;
	}

	function getProjectMember( $project ){
		if( $this->logged['PRIVILEGE'] == "EMP"){
			$this->db->where("EMPLOYEE", $this->logged['EMPLOYEE']);
		}
		return $this->db->select("PROJECTMEMBERID as keydt, employees.NAME as valuedt, employees.MOBILE whatsapp, projectunits.NAME AS projectunit")
			->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
			->join("projectunits", "projectmembers.PROJECTUNIT=PROJECTUNITID", "LEFT")
			->where("PROJECT", $project)
			->get("projectmembers")->result_array();
	}

	function getEmployeeIdByMember( $projectMember ){
		$emp = $this->db->select("EMPLOYEE")->where("PROJECTMEMBERID", $projectMember)->get("projectmembers")->row();
		return $emp->EMPLOYEE;
	}

	function getCustomertype(){
		return $this->db->select("CUSTOMERTYPEID as keydt, NAME as valuedt")->get("customertypes")->result_array();
	}

	function getEventtype(){
		return $this->db->select("EVENTTYPEID as keydt, NAME as valuedt")->get("eventtypes")->result_array();
	}

	function getEmployeeStatus(){
		return $this->db->select("EMPLOYEESTATUSID as keydt, NAME as valuedt")->get("employeestatuses")->result_array();
	}

	function getBusinessField(){
		return $this->db->select("BUSINESSFIELDID as keydt, NAME as valuedt")->get("businessfields")->result_array();
	}

	function showVendorBusinessFiled( $id ){
		$data = $this->db->select("BUSINESSFIELD")->where("VENDORID", $id)->get("vendors")->row();
		$result = "";
		if( $data->BUSINESSFIELD != "" ){
			$fields = $this->db->where("BUSINESSFIELDID IN (".$data->BUSINESSFIELD.")")->get("businessfields")->result();
			foreach($fields as $f){
				$result .= $f->NAME . ", ";
			}
			$result = substr($result,0,strlen($result)-2);
		}
		return $result;
	}

	function countDuration($args){
		$start = date_create($args[0]);
		$end = date_create($args[1]);
		$diff=date_diff($start,$end);
		return $diff->days;
	}


	function showInventory($args){
		$inventory = $this->db->select("inventories.*, projectquotations.QTY, projectquotations.SQM, unittypes.NAME UNITTYPE")
		->where('PROJECT',$args)
		->join('inventories','INVENTORYID = INVENTORY', 'LEFT')
		->join('unittypes', 'UNITTYPEID = UNITTYPE' , 'LEFT' )
		->order_by("projectquotations.ORDERSEQ")
		->get('projectquotations')->result();
		$res = "";
		if (count($inventory)>0) {
				foreach ($inventory as $key => $value) {
						$no = $key+1;
						if (!$value->UNITTYPE) {
							$ut = "unit";
						}
						else{
							$ut = $value->UNITTYPE;
						}
						$qty = $value->QTY." ".$ut;
						if ($value->SQM) {
							$qty = $value->SQM." SQM";
						}
						$spec = "";
						if ($value->SPECIFICATION) {
							$spec = "&nbsp; - ".$value->SPECIFICATION."<br />";
						}
						$res.= $no.". " . $value->NAME." | ".$qty."<br />".$spec;
				}
				// $res = "<pre>".print_r($inventory,TRUE);
		}
		else{
			$res = "-";
		}
		return $res;
	}


	function showMonitorInventory($args){
		$inventory = $this->db->select("inventories.*, projectquotations.QTY, projectquotations.SQM, unittypes.NAME UNITTYPE,projectquotations.DESCRIPTION")
		->where('PROJECT',$args)
		->join('inventories','INVENTORYID = INVENTORY', 'LEFT')
		->join('unittypes', 'UNITTYPEID = UNITTYPE' , 'LEFT' )
		->order_by("projectquotations.ORDERSEQ")
		->get('projectquotations')->result();
		$res = "";
		if (count($inventory)>0) {
				foreach ($inventory as $key => $value) {
					// dd($value);
						$no = $key+1;
						if (!$value->UNITTYPE) {
							$ut = "unit";
						}
						else{
							$ut = $value->UNITTYPE;
						}
						$qty = $value->QTY." ".$ut;
						if ($value->SQM) {
							$qty = $value->SQM." SQM";
						}
						$spec = "";
						if ($value->SPECIFICATION) {
							$spec = "&nbsp; - ".$value->SPECIFICATION."<br />"; // unused
						}
						$res.= $no.". " . $value->NAME." | ".$qty."<br />".$value->DESCRIPTION."<br />";
				}
				// $res = "<pre>".print_r($inventory,TRUE);
		}
		else{
			$res = "-";
		}
		return $res;
	}

	function showInventoryLimit($args, $limit=10){
		$res = $this->showMonitorInventory($args);
    $lines = explode("<br />", $res);
    $btn = "";
    if (count($lines)>=$limit) {
    	$btn = "<br /><button onClick='inventoryDetail(".$args.")' class='btn btn-warning btn-sm'><i class='fas fa-list'></i> Selengkapnya</buttom>";
    }

    $lines = array_slice($lines, 0, $limit); //$limit is how many lines you want to keep
    $res = implode("<br />", $lines);
		return $res.$btn;
	}

	function showInventoryLimitOld($args, $limit=10){
		$res = $this->showInventory($args);
    $lines = explode("<br />", $res);
    $btn = "";
    if (count($lines)>=$limit) {
    	$btn = "<br /><button onClick='inventoryDetail(".$args.")' class='btn btn-warning btn-sm'><i class='fas fa-list'></i> Selengkapnya</buttom>";
    }

    $lines = array_slice($lines, 0, $limit); //$limit is how many lines you want to keep
    $res = implode("<br />", $lines);
		return $res.$btn;
	}

	function showSupplierBusinessFiled( $id ){
		$data = $this->db->select("BUSINESSFIELD")->where("SUPPLIERID", $id)->get("suppliers")->row();
		$result = "";
		if( $data->BUSINESSFIELD != "" ){
			$fields = $this->db->where("BUSINESSFIELDID IN (".$data->BUSINESSFIELD.")")->get("businessfields")->result();
			foreach($fields as $f){
				$result .= $f->NAME . ", ";
			}
			$result = substr($result,0,strlen($result)-2);
		}
		return $result;
	}

	function getProjectDetail($projectId){
		return $this->db->select("projects.*, projecttypes.TAXPERCENT, customercontacts.NAME CUSTOMERCONTACTNAME, 
		                         customercontacts.PHONE CUSTOMERCONTACTPHONE, 
		                         customercontacts.EMAIL CUSTOMERCONTACTEMAIL, 
		                         eventtypes.NAME as EVENTTYPE,
		                         APPROVALSTATUS,
		                         projectstages.CLASS, projectstages.NAME as PROJECTSTAGENAME, 
		                         customers.NAME as CUSTOMERNAME, employees.NAME as EMPLOYEENAME, employees.MOBILE")
			->where("PROJECTID", $this->session->userdata("PROJECTID"))
			->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
			->join("customercontacts","CUSTOMERCONTACTID=CUSTOMERCONTACT", "LEFT")
			->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
			->join("projectstages", "PROJECTSTAGE=PROJECTSTAGEID", "LEFT")
			->join("eventtypes", "EVENTTYPE=EVENTTYPEID", "LEFT")
			->join("projecttypes", "PROJECTTYPE=PROJECTTYPEID", "LEFT")
			->get("projects")->row();
	}

	function getProjectDetailbyKode($projectCode){
		return $this->db->select("projects.*, customercontacts.NAME CUSTOMERCONTACTNAME, customercontacts.PHONE CUSTOMERCONTACTPHONE, APPROVALSTATUS,projectstages.CLASS, projectstages.NAME as PROJECTSTAGENAME, customers.NAME as CUSTOMERNAME, employees.NAME as EMPLOYEENAME, employees.MOBILE")
			->where("PROJECTORDERCODE", $projectCode)
			->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
			->join("customercontacts","CUSTOMERCONTACTID=CUSTOMERCONTACT", "LEFT")
			->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
			->join("projectstages", "PROJECTSTAGE=PROJECTSTAGEID", "LEFT")
			->get("projects")->row();
	}

	function getProject($id=NULL){
		if ($id) {
			$this->db->where('PROJECTID',$id);
		}

		return $this->db->get('projects')->result_array();
	}

	function getProjects( $where = null ){
		if( isset($where) ){
			$this->db->where( $where );
		}
		return $this->db->select("PROJECTID as keydt, CONCAT( projects.PROJECTORDERCODE, ' | ', customers.NAME, ' | ', PROJECTLOCATION) as valuedt")
		->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
		->get('projects')->result_array();
	}

	function getProjectQuotation($id=NULL){
		return $this->db->select('')
		->where('PROJECT',$id)->get('projectquotations')->result_array();
	}

	function getProjectQuotationDetailed($id=NULL){
		return $this->db->query("SELECT PROJECTQUOTATIONID,
		projects.NAME PROJECT, DURATION,
		inventories.NAME INVENTORY, 
		projectquotations.QTY, 
		projectquotations.AVAILABLEQTY,
		projectquotations.LACKQTY,
		FINALCOST, COST, DISCOUNT, DESCRIPTION, SQM, COSTSQM, (FINALCOST+DISCOUNT) TOTAL,
		STATUS,
		unittypes.NAME UNITTYPE,
		projectquotations.CREATEBY, projectquotations.UPDATEBY, projectquotations.CREATEAT, projectquotations.UPDATEAT
		FROM projectquotations
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN inventories ON INVENTORYID = INVENTORY
		LEFT JOIN unittypes ON UNITTYPEID = UNITTYPE
		WHERE PROJECT='".$id."'
		ORDER BY ORDERSEQ ASC")->result_array();
	}


	function getProjectQuotationSubrent( $project = null ){
		if( isset($project) ){
			$this->db->where("PROJECT", $project);
		}
		return $this->db->select("PROJECTQUOTATIONID as keydt, inventories.NAME as valuedt")
		->join("inventories", "INVENTORY=INVENTORYID", "LEFT")
		// ->where("projectquotations.LACKQTY > ", 0)
		->get("projectquotations")->result_array();
	}

	function getProjectSubrentDetail( $projectSubrent = null ){
		if( isset($projectSubrent) ){
			$this->db->query('');
		}
		else{
			return false;
		}
	}


	function splitLokasi( $lokasi ){
		if( is_array($lokasi) ){
			$lokasi = explode(",", $lokasi[0]);
			return $lokasi[0];
		} else {
			$lokasi = explode(",", $lokasi);
			return $lokasi[0];
		}
	}

	function getTotalSQM( $id ){
		$project = $this->db->select("SUM(SQM) as SQM")->where("PROJECT", $id)->get("projectquotations")->row();
		return $project->SQM;
	}

	function getOpLeader( $id ){
		$project = $this->db->select("employees.NAME as EMPLOYEE, projectunits.NAME as PROJECTUNIT")
							->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
							->join("projectunits", "PROJECTUNIT=PROJECTUNITID", "LEFT")
							->where("PROJECT", $id)->get("projectmembers")->result();
		$op = "";
		foreach($project as $pr){
			$op .= '- ' .$pr->EMPLOYEE . " (".$pr->PROJECTUNIT.") <br />";
		}
		return $op;
	}

	function getAccountName( $value ){
		$nama = explode(" ", $value[0]);
		return @$nama[0] . " " . @$nama[1];
	}

	function getProjectProgress( $id ){
		$result = "";
		// Check Quotation, Member, Ada subrent apa enggak
		// Check Quotation
		$quotation = $this->db->where("PROJECT", $id)->get("projectquotations")->num_rows();
		if( $quotation > 0 ){
			$result .= "<i class='fa fa-list fa-2x text-success me-3' title='Sudah Ada Penawaran'></i>";
		} else {
			$result .= "<i class='fa fa-list fa-2x text-secondary me-3' title='Belum ada Penawaran'></i>";
		}
		// Check Member
		$member = $this->db->where("PROJECT", $id)->get("projectmembers")->num_rows();
		if( $member > 0 ){
			$result .= "<i class='fa fa-users fa-2x text-danger me-3' title='Sudah Ada Anggota'></i>";
		} else {
			$result .= "<i class='fa fa-users fa-2x text-secondary me-3' title='Belum ada Anggota'></i>";
		}

		// Check subrent
		$subrent = $this->db->where("PROJECT", $id)->where("LACKQTY >", "0")->get("projectquotations")->num_rows();
		if( $subrent > 0 ){
			$result .= "<i class='fa fa-code-branch fa-2x text-warning me-3' title='Ada Subrent'></i>";
		} else {
			$result .= "<i class='fa fa-code-branch fa-2x text-secondary me-3' title='Tidak Subrent'></i>";
		}
		return $result;

	}

	function getSendEmailStatus($data=NULL){
		$result = "";
		if( $data[0] > 0 ){
			$result .= "<i class='fa fa-envelope fa-2x text-success me-3' title='Sudah Dikirim'></i>";
		} else {
			$result .= "<i class='fa fa-envelope fa-2x text-secondary me-3' title='Belum Dikirim'></i>";
		}
		return $result;
	}

	function hitungUlangStock( $projectId ){
		$quotations = $this->db->where("PROJECT", $projectId)->get("projectquotations")->result();
		foreach( $quotations as $quo ){
			$inv = $quo->INVENTORY;
			$project = $this->db->where("PROJECTID", $projectId)->get("projects")->row();
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
			$this->db->update("projectquotations", array(
				"AVAILABLEQTY" => $avail,
				"AVAILABLESQM" => $avail_sqm,
				"LACKQTY" => ($avail < $quo->QTY) ? $quo->QTY - $avail : 0,
			), array("PROJECTQUOTATIONID", $quo->PROJECTQUOTATIONID));
		}
	}

	function checkKesiapanProject(){
		// Check Kesiapan Inventory

	}

	function w2pdf($input_path,$output_path){
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			chdir('C:\\Program Files\\LibreOffice\\program');
			$r = shell_exec(' soffice --headless --convert-to pdf '.$input_path.' --outdir '.$output_path.'');
		} else {
			$r = shell_exec('export HOME=/tmp && soffice --headless --convert-to pdf '.$input_path.' --outdir '.$output_path.'');
		}
		return $r;
	}

	function showSubrents( $project ){
		$subrents = $this->db->select("PROJECTSUBRENTDETAILID, VENDORID,
		                              projectsubrentdetails.QTY,
		                              projectquotations.SQM,
		                              unittypes.NAME UNITTYPE,
		                              inventories.NAME as INVENTORY,projectquotations.DESCRIPTION,
		                              vendors.NAME as VENDOR, projectsubrentdetails.NOTES")
					->where("projectsubrents.PROJECT", $project)
					->join("projectsubrents", "PROJECTSUBRENT=PROJECTSUBRENTID", "LEFT")
					->join("projectquotations", "PROJECTQUOTATION=PROJECTQUOTATIONID", "LEFT")
					->join("inventories", "projectquotations.INVENTORY=INVENTORYID", "LEFT")
					->join("vendors", "VENDOR=VENDORID", "LEFT")
					->join("unittypes", "UNITTYPEID=UNITTYPE","LEFT")
					->get("projectsubrentdetails")->result();
		$result = "";
		if( count($subrents) > 0 ){
			$result .= "<strong>Subrent</strong> <br />Vendor :";
		}
		$lockvendor = "";
		foreach($subrents as $sr){
			$vendor = "<br /><strong>".$sr->VENDOR."</strong><br />";
			if ($lockvendor==$sr->VENDORID) {
				$vendor = "";
			}
			if (!$sr->UNITTYPE) {
				$ut = "unit";
			}
			else{
				$ut = $sr->UNITTYPE;
			}
			$qty = $sr->QTY." ".$ut;
			if ($sr->SQM) {
				$qty = ($sr->QTY/4)." SQM";
			}
			$result .= $vendor." &nbsp; - " . $sr->INVENTORY . " | ".$qty. (($sr->NOTES != "" ) ? " | Spesifikasi : " . $sr->NOTES : "") . "<br />";
			$lockvendor = $sr->VENDORID;
		}
		return $result;
	}

	function getSubrentNumber(){
		$subrent = $this->db->order_by("PROJECTSUBRENTID", "DESC")->limit(1)->get("projectsubrents")->row();
		if( isset($subrent->PROJECTSUBRENTID) ){
			return str_pad($subrent->PROJECTSUBRENTID + 1,5,"0",STR_PAD_LEFT);
		} 
		return str_pad(1,5,"0",STR_PAD_LEFT);
	}

	function getUnitType(){
		return $this->db->select("UNITTYPEID as keydt, NAME as valuedt")->get("unittypes")->result_array();
	}

	function getProjectDuration( $project ){
		return $this->db->get_where("projects", array( "PROJECTID" => $project) )->row()->PROJECTDURATION;
	}
	
}
