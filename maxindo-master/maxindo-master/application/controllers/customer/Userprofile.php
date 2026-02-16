<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userprofile extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
		$this->getparams();
	}
	
	function index(){
		$this->edit();
	}
	
	function getparams(){
		$this->params['command'] = "browse";
		$this->params['name'] = $this->lang->line("userprofile");
		$this->params['table'] = "users";
		$this->params['sql'] = "SELECT USERID, users.NAME,
		CONCAT('<div class=\"d-flex align-items-center\">
      <div class=\"symbol symbol-50 mr-4\">
        ',imageload(PHOTO, users.NAME,\"".base_url("uploads")."\"),'
      </div>
      <div class=\"user_detail\">
        <span class=\"user_name font-weight-bold text-primary d-block\">',users.NAME,'</span>
        <span class=\"user_email d-block text-muted\">@',USERID,'</span>
      </div>
  	</div>') as USERNAME,
		CONCAT('M :',IFNULL(users.MOBILE,''),'<br />E : ', IFNULL(users.EMAIL,'')) as CONTACT, 
		PRIVILEGE, USERSTATUS, STATUSISI,
		PHOTO
		FROM users 
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
			'ID' => array(
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
			'USERSTATUS' => array(
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getUserStatus(),
				'width' => "100px"
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'ID' => array(
				'hidden' => true,
				'type' => "primarykey"
			),
			'USERID' => array(
				'class' => "col-md-3",
				'disabled' => true
			),
			'PASSWD' => array(
				'class' => "col-md-6",
				'type' => "password",
				'help' => "Kosongi = tanpa mengganti password"
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'MOBILE' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'EMAIL' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'type' => "email"
			),
			'ADDRESS' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'type' => "textarea"
			),
			'PRIVILEGE' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getPrivilege(),
				'disabled' => true,
			),
			'PHOTO' => array(
				'class' => "col-md-3",
				'type' => "file"
			),
			'USERSTATUS' => array(
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getUserStatus(),
				'disabled' => true
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

	function add(){
		if(count($_POST)>0){
			$_POST['PASSWD'] = md5($_POST['PASSWD']);
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->params["urisegments"]['pk'] = "ID";
		$this->params["urisegments"]["valpk"] = $this->logged['ID'];

		unset($this->params['fieldadd']['PASSWD']['validation']);
		if(count($_POST)>0){
			if( $_POST['PASSWD'] == ""){
				unset($_POST['PASSWD']);
			}
			else {
				$_POST['PASSWD'] = md5($_POST['PASSWD']);
			}
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
			</script>";
		}
	}
	
}
?>