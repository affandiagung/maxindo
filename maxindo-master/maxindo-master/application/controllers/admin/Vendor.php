<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	
	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall,search";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "vendors";
		$this->params['sql'] = "SELECT VENDORID,vendors.NAME,cities.NAME AS CITY,ADDRESS, PKP, PPH, PHONE,EMAIL,BUSINESSFIELD
		FROM vendors
		LEFT JOIN cities ON CITYID = CITY
		WHERE 1
		";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();

		$this->params['search'] = array(
			'BUSINESSFIELD' => array(
				'type' => "dropdownquery",
				'class' => "col-md-6 select2",
				'sourcequery' => blankoption($this->Mmasterdata->getBusinessField())
			)
		);
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'VENDORID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'BUSINESSFIELD' => array(
				'class' => "sorting",
				'type' => "function",
				'model' => "Mmasterdata",
				'func' => "showVendorBusinessFiled"
			),
			'PKP' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
			),
			'PPH' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
			),
			'CITY' => array(
				'class' => "sorting",
			),
			'ADDRESS' => array(
				'class' => "sorting",
			),
			'CONTACTNAME1' => array(
				'class' => "sorting",
			),
			'CONTACTPHONE1' => array(
				'class' => "sorting",
			),
			'CONTACTEMAIL1' => array(
				'class' => "sorting",
				'type' => "email",
			),
			/*'PHONE' => array(
				'class' => "sorting",
			),
			'EMAIL' => array(
				'class' => "sorting",
			),*/
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'VENDORID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'GENERALINFO'  => array(
				'type' => "separator",
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'BUSINESSFIELD' => array(
				'validation' => "required",
				'multiple' => true,
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getBusinessField(),
			),
			'PKP' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => blankarray($this->Mmasterdata->getYesNo()),
			),
			'PPH' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => blankarray($this->Mmasterdata->getYesNo()),
			),
			'CITY' => array(
				// 'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getCity())
			),
			'ADDRESS' => array(
				// 'validation' => "required",
				'class' => "col-md-6",
				'type' => "textarea",
			),
			/*'PHONE' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'EMAIL' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),*/
			/*'CONTACTNAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'CONTACTMOBILE' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),*/
			'BANKINFO' => array(
				'type' => "separator",
			),
			'BANKNUMBER1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNAME1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'ACCOUNTNAME1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNUMBER2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNAME2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'ACCOUNTNAME2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNUMBER3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNAME3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'ACCOUNTNAME3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTINFO' => array(
				'type' => "separator",
			),
			'CONTACTNAME1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTPHONE1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTEMAIL1' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "email",
			),
			'CONTACTJOBPOSITION1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTNAME2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTPHONE2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTEMAIL2' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "email",
			),
			'CONTACTJOBPOSITION2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTNAME3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTPHONE3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTEMAIL3' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "email",
			),
			'CONTACTJOBPOSITION3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		if( count($_POST) > 0 ){
			if( $_POST['BUSINESSFIELD'] != "" ){
				$_POST['p_search'] = " AND FIND_IN_SET('".$_POST['BUSINESSFIELD']."', BUSINESSFIELD)";
			}
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		if (count($_POST)>0) {
			@$_POST['BUSINESSFIELD'] = implode(',',@$_POST['BUSINESSFIELD']);
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		if (count($_POST)>0) {
			@$_POST['BUSINESSFIELD'] = implode(',',@$_POST['BUSINESSFIELD']);
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
	
}
