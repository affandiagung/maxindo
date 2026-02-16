<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		$this->params['name'] = $this->lang->line("user");
		$this->params['table'] = "users";
		$this->params['sql'] = "SELECT USERID, users.NAME,
		CONCAT('<div class=\"d-flex align-items-center\">
      <div class=\"symbol symbol-50 me-4\">
        ',imageload(users.PHOTO, users.NAME,\"".base_url("uploads")."\"),'
      </div>
      <div class=\"user_detail\">
        <span class=\"user_name font-weight-bold text-primary d-block\">',users.NAME,'</span>
        <span class=\"user_email d-block text-muted\">@',USERID,'</span>
      </div>
  	</div>') as USERNAME,
		CONCAT('M :',IFNULL(users.MOBILE,''),'<br />E : ', IFNULL(users.EMAIL,'')) as CONTACT, 
		PRIVILEGE, USERSTATUS,
		users.PHOTO,
		employees.NAME as EMPLOYEE,
		customers.NAME as CUSTOMER
		FROM users
		LEFT JOIN employees ON EMPLOYEE=EMPLOYEEID
		LEFT JOIN customers ON CUSTOMER=CUSTOMERID
		";

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
			'USERID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'USERNAME' => array(
				'class' => "sorting",
				'width' => "200px"
			),
			'CONTACT' => array(
				'class' => "sorting",
				'width' => "150px"
			),
			'PRIVILEGE' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getPrivilege()
			),
			'EMPLOYEE' => array(
				'class' => "sorting",
			),
			'USERSTATUS' => array(
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getUserStatus(),
				'width' => "100px"
			),

		);

	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'USERID' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'maxlength' => "25",
				'type' => "primarykey"
			),
			'PASSWD' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "50",
				'type' => "password",
				'help' => "Kosongi = tanpa mengganti password"
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "50",
			),
			'MOBILE' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "50",
			),
			'EMAIL' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "50",
				'type' => "email"
			),
			'PRIVILEGE' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getPrivilege()
			),
			'EMPLOYEE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee())
			),
			'CUSTOMER' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getCustomer())
			),
			'PHOTO' => array(
				'class' => "col-md-3",
				'type' => "file"
			),
			'USERSTATUS' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getUserStatus()
			),
		);
		if($this->logged['PRIVILEGE'] == "SKPD"){
			unset($this->params['fieldadd']['IDREF']);
			unset($this->params['fieldadd']['PEMBINA']);
			$this->params['fieldadd']['PRIVILEGE']['sourcearray'] = $this->Mmasterdata->getPrivilegeSKPD( $this->logged['IDREF'] );
			$this->params['fieldadd']['KPA']['sourcequery'] = array_merge(array(0 => array("keydt" => null, "valuedt" => "-")),$this->Mmasterdata->getKPA( $this->logged['IDREF'] ));
			$this->params['fieldadd']['PPK']['sourcequery'] = array_merge(array(0 => array("keydt" => null, "valuedt" => "-")),$this->Mmasterdata->getPPK( $this->logged['IDREF'] ));
		}
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
		echo "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		if(count($_POST)>0){
			$_POST['PASSWD'] = md5($_POST['PASSWD']);
			if( $this->logged['PRIVILEGE'] == "SKPD"){
				$_POST['IDREF'] = $this->logged['IDREF'];
			}
		}
		$this->params['fieldadd']['EMAIL']['validation'] .= "|is_unique[users.EMAIL]";
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		unset($this->params['fieldadd']['PASSWD']['validation']);
		if(count($_POST)>0){
			if( $_POST['PASSWD'] == ""){
				unset($_POST['PASSWD']);
			}
			else {
				$_POST['PASSWD'] = md5($_POST['PASSWD']);
			}

			if( $this->logged['PRIVILEGE'] == "SKPD"){
				$_POST['IDREF'] = $this->logged['IDREF'];
			}
			// Check Email
			$check = $this->db->where("EMAIL", $_POST['EMAIL'])->where("USERID != ",$this->urisegments['valpk'])->get("users")->result();
			if( count($check) > 0 ){
				$this->params['alert'] = array(
					'type' => "danger",
					'title' => "error",
					'message' => "Email ini sudah digunakan"
				);
				unset($_POST['EMAIL']);
			}
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		// Check
		$check = $this->db->get("users")->num_rows();
		if($check == 1){
			echo "Anda Tidak dapat menghapus user terakhir";
		} else {
			$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
			if($delete){
				echo "<script>
					loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
				</script>";
			}
		}
	}
	
}
?>