<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuration extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->edit();
	}
	
	function getparams(){
		$this->params['command'] = "browse";
		$this->params['name']= $this->lang->line("configuration");
		$this->params['table']="configurations";
		$this->params['sqlupdate']="SELECT * FROM configurations";
		$this->urisegments=$this->uri->uri_to_assoc(4);
		$this->getfieldedit();
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'CONFIGURATIONID' => array(
				'hidden' => true,
				'class' => "col-md-2",
				'maxlength' => "25",
				'type' => "primarykey"
			),
			'SIGNATURECONFIG' => array(
				'type' => "separator"
			),
			'TTD1NAME' => array(
				'class' => "col-md-6",
			),
			'TTD1JABATAN' => array(
				'class' => "col-md-6",
			),
			// 'TTD1NIP' => array(
			// 	'class' => "col-md-6",
			// ),
			'EMAILCONFIG' => array(
				'type' => "separator"
			),
			'SMTP_HOST' => array(
				'is_required' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'SMTP_NAME' => array(
				'is_required' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'SMTP_USER' => array(
				'is_required' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'SMTP_PASSWORD' => array(
				'is_required' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'SMTP_SECURE' => array(
				'is_required' => "required",
				'class' => "col-md-6",
				'type' => "dropdownarray",
				'sourcearray' => array('tls' => "TLS", 'ssl' => "SSL")
			),
			'SMTP_PORT' => array(
				'is_required' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'MANUALCONFIG' => array(
				'type' => "separator"
			),
			'MANUALBOOK' => array(
				'class' => "col-md-6",
				'type' => "file"
			),
			'OFFICECONFIG' => array(
				'type' => "separator"
			),
			'OFFICE_NAME' => array(
				'class' => "col-md-6",
				'validation' => "required"
			),
			'OFFICE_ADDRESS' => array(
				'class' => "col-md-6",
				'validation' => "required",
				'type' => "textarea"
			),
			'OFFICE_EMAIL' => array(
				'class' => "col-md-6",
				'validation' => "required",
			),
			'OFFICE_PHONE' => array(
				'class' => "col-md-6",
				'validation' => "required",
			),
			'APPCONFIG' => array(
				'type' => "separator"
			),
			'APP_NAME' => array(
				'class' => "col-md-6",
				'validation' => "required"
			),
			'APP_SHORTNAME' => array(
				'class' => "col-md-6",
				'validation' => "required"
			),
			'APP_LOGO_HEADER' => array(
				'class' => "col-md-3",
				'validation' => "required",
				'type' => 'file'
			),
			'APP_LOGO_FOOTER' => array(
				'class' => "col-md-3",
				'type' => 'file'
			),
			/*'APP_PROVINCE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getProvince(),
				'validation' => "required",
			),
			'KODE_KLDI' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getInstansi(),
				'validation' => "required",
			),*/
			'SYSTEMCONFIG' => array(
				'type' => "separator"
			),
			'GOOGLE_MAP_API' => array(
				'class' => "col-md-8",
				'validation' => "required",
			),
			'MAP_DEFAULT_LOCATION' => array(
				'class' => "col-md-6",
				'type' => "map",
				'validation' => "required",
			),
			'RENTALCONFIG' => array(
				'type' => "separator"
			),
			'WAREHOUSENUMBER' => array(
				'class' => "col-md-6",
				'help' => "62xxx"
			),
			'OPERATIONALNUMBER' => array(
				'class' => "col-md-6",
				'help' => "62xxx"
			),
			'MARKETINGMANAGERNUMBER' => array(
				'class' => "col-md-6",
				'help' => "62xxx"
			),
			'PERSONILBREAKDAY' => array(
				'class' => "col-md-3",
				'type' => "number",
				'help' => " jam"
			),
			'INVENTORYBREAK' => array(
				'class' => "col-md-3",
				'type' => "number",
				'help' => " jam"
			),
		);
	}
	
	
	function browse(){
		$this->edit();
	}
	
	function edit(){
		$this->params['urisegments']['pk'] = 'CONFIGURATIONID';
		$this->params['urisegments']['valpk'] = 1;
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
}
?>