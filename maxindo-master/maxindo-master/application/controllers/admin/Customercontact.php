<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customercontact extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
		$this->getparams();
	}
	
	function index(){
		if( isset($this->urisegments['valpk'])){
			$this->session->set_userdata( "customer", $this->urisegments['valpk'] );
		}
		$this->browse();
	}
	
	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		$this->params['maincontent'] = "customercontact";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "customercontacts";
		$this->params['sql'] = "SELECT customercontacts.CUSTOMER,customercontacts.KTP,customercontacts.NPWP,
		customercontacts.PHONE AS WHATSAPP,
		customercontacts.NAME,customercontacts.PHONE,customercontacts.EMAIL,customercontacts.JOBPOSITION,
		customercontacts.CUSTOMERCONTACTID, customercontacts.CREATEBY, customercontacts.UPDATEBY, customercontacts.CREATEAT, customercontacts.UPDATEAT

		FROM customercontacts
		LEFT JOIN customers ON customercontacts.CUSTOMER=customers.CUSTOMERID";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(

			),
			'#' => array(
				'type' => "checkbox"
			),
			'CUSTOMERCONTACTID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'PHONE' => array(
				'class' => "sorting",
			),
			'WHATSAPP' => array(
				'class' => "sorting",
				'ltag' => "<a class='btn btn-success' target='_blank' href='https://wa.me/",
				'rtag' => "?text='><i class='fab fa-whatsapp'></i>Whatsapp</a>",
			),
			'EMAIL' => array(
				'class' => "sorting",
			),
			'JOBPOSITION' => array(
				'class' => "sorting",
			),
			'KTP' => array(
				'type' => "image",
				'width' => "150px"
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
		$this->params['sql'] .= " WHERE CUSTOMER='".$this->session->userdata("customer")."'";
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		if( count($_POST) > 0 ){
			$_POST['CUSTOMER'] = $this->session->userdata("customer");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		if( count($_POST) > 0 ){
			$_POST['CUSTOMER'] = $this->session->userdata("customer");
			$_POST['UPDATEBY'] = $this->logged['USERID'];
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