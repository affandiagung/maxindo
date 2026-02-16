<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_detail extends CI_Controller {

	private $logged = array();
	private $params = array();
	private $customer_name = "";

	public function __construct(){
		parent::__construct();
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->logged = $this->session->userdata("admin");
		$this->getparams();
	}

	public function index(){
		if( isset($this->urisegments['valpk']) ){
			$this->session->set_userdata("CUSTOMERID", $this->urisegments['valpk']);
		}
		if( $this->session->has_userdata("CUSTOMERID")){
			$customer = $this->getCustomerData( $this->session->userdata("CUSTOMERID") );
			$this->session->set_userdata("customer", $customer);
			$data['customer'] = $customer;
			$data['PRIVILEGE'] = $this->logged['PRIVILEGE'];
			$this->load->view("admin/customer_detail", $data);
		} else {
			echo "Invalid parameter";
		}
	}

	function getCustomerData($id){
		return $this->db->query("SELECT CUSTOMERID,
		customers.NAME,
		CONCAT(ADDRESS,'<br />',districts.NAME,'<br />', cities.NAME,'<br />', provinces.NAME) as ADDRESS,
		-- CONCAT('P : ', PHONE,'<br />E : ',EMAIL) as CONTACT,
		-- CONCAT(CONTACTNAME,'<br />',CONTACTMOBILE) as CP, 
		REGISTERDATE, customertypes.NAME CUSTOMERTYPE, CUSTOMERCOORDINATE,
		CREATEBY, UPDATEBY, CREATEAT, UPDATEAT
		FROM customers
		LEFT JOIN provinces ON PROVINCE=PROVINCEID
		LEFT JOIN cities ON CITY=CITYID
		LEFT JOIN districts ON DISTRICT=DISTRICTID
		LEFT JOIN customertypes ON CUSTOMERTYPE = CUSTOMERTYPEID
		WHERE CUSTOMERID = ".$this->db->escape($id))->row();

	}

	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function getparams(){
		$this->params['command'] = "browse";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "customer_detail";
				$this->params['table'] = "projects";
		$this->params['sql'] = "SELECT projects.*,
		employees.NAME EMPLOYEENAME,
		customers.NAME as CUSTOMERNAME,
		customers.ADDRESS as CUSTOMERADDRESS,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') as PROJECTSTAGENAME,
		projecttypes.NAME as PROJECTTYPENAME
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID
		WHERE 1 AND CUSTOMER = '".$this->session->userdata('CUSTOMERID')."'";
		$this->params['sqlupdate'] = $this->params['sql'];
		$this->params['order'] = "PROJECTID DESC";
		$this->params['query-total'] = "SELECT SUM(TOTAMOUNT) as TOTAMOUNT
		FROM projects
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		WHERE CUSTOMERID='".$this->session->userdata("CUSTOMERID")."'";

		if( $this->logged['PRIVILEGE'] == "EMP"){
			$this->params['sql'] .= " AND projectactivities.EMPLOYEE = '".$this->logged['EMPLOYEE']."'";
			$this->params['command'] = "browse,add";
		}

		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'PROJECTID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'PROJECTORDERCODE' => array(
				'class' => "sorting",
			),
			'PROJECTLOCATION'=> array(
				'class' => 'sorting',
				'type' => "function",
				'func' => "splitLokasi",
				'model' => "Mmasterdata",
				'params' => "PROJECTLOCATION"
			),
			'PROJECTSTAGENAME' => array(
				'class' => "sorting",
			),
			'PROJECTSTART' => array(
				'class' => "sorting",
				'type' => "date",
			),
			'PROJECTEND' => array(
				'class' => "sorting",
				'type' => "date",
			),
			'EMPLOYEENAME' => array(
				'class' => "sorting",
				'type' => "",
				'sumtitle' => true,
			),
			'TOTAMOUNT' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true,
			),
			'PAYMENTSTATUS' => array(
				'class' => "sorting",
				// 'type' => "dropdownquery",
				// 'sourcequery' => $this->Mmasterdata->getPaymentStatus(),
			), 
		);
	}
	function update_(){
		$post = $this->input->post();
	}

}

/* End of file Project_detail.php */
/* Location: ./application/controllers/admin/Project_detail.php */