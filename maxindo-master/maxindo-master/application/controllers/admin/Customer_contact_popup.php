<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_contact_popup extends CI_Controller{
	
	private $params = array();
	private $province = "";
	private $visi = array();
	
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
		$this->urisegments=$this->uri->uri_to_assoc(4);
		if( isset($this->urisegments['trigger']) ){
			$this->session->set_userdata("trigger", $this->urisegments['trigger']);
		}
		$this->params['simpleform'] = true;
		$this->params['maincontent'] = "customer_popup";
		$this->params['command'] = "browse,add,edit";
		$this->params['name']=$this->lang->line( $this->router->fetch_class() );
		$this->params['table']="customercontacts";
		$this->params['sql'] = "SELECT customercontacts.CUSTOMER,
		customercontacts.PHONE AS WHATSAPP, customercontacts.KTP, customercontacts.NPWP,
		customercontacts.NAME,customercontacts.PHONE,customercontacts.EMAIL,customercontacts.JOBPOSITION,
		customercontacts.CUSTOMERCONTACTID,
		customercontacts.CREATEBY, customercontacts.UPDATEBY, customercontacts.CREATEAT, customercontacts.UPDATEAT
		FROM customercontacts
		LEFT JOIN customers ON customercontacts.CUSTOMER=customers.CUSTOMERID
		WHERE CUSTOMER = '".$this->session->userdata("customerId")."'";
		$this->getfieldselect();
		$this->getfieldedit();
		
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "setCustomerContact(\"".$this->session->userdata("trigger")."\",\"[id]\");";

	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'CUSTOMERCONTACTID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => "sorting",
				'type' => "",
			),
			'PHONE' => array(
				'class' => "sorting",
			),
			'EMAIL' => array(
				'class' => "sorting",
			),
			'KTP' => array(
				'class' => "sorting",
				'type' => "image",
				'width' => "100px"
			),
		);
	}

	function getfieldedit(){
		$this->params['fieldadd']=array(
			'CUSTOMERCONTACTID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "col-md-6",
				'validation' => "required"
			),
			'PHONE' => array(
				'class' => "col-md-6",
				'help' => "Format internasional, ex : 6281382777396",
				'validation' => "required"
			),
			'EMAIL' => array(
				'class' => "col-md-6",
				'type' => "email",
			),
			'JOBPOSITION' => array(
				'class' => "col-md-6",
			),
			'KTP' => array(
				'class' => "col-md-6",
				'type' => "file"
			),
			'NPWP' => array(
				'class' => "col-md-6",
				'type' => "file"
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
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function add(){
		if( count($_POST) > 0 ){
			$_POST['CUSTOMER'] = $this->session->userdata("customerId");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		if( count($_POST) > 0 ){
			$_POST['CUSTOMER'] = $this->session->userdata("customerId");
			$_POST['UPDATEBY'] = $this->logged['USERID'];
		}
		$this->jsinclude();
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function jsinclude(){
	}
}